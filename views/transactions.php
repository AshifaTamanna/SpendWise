<?php
session_start();

require_once "../models/mydb.php";

if(!isset($_SESSION["user_id"]))
    {
        header("Location: ../login.php");
        exit();
    }
$db = new MYDB();
$conn = $db->createConn();

$userId = $_SESSION["user_id"];

// Get month/year from GET or use current
$currentMonth = date('m');
$currentYear = date('Y');

$selectedMonth = isset($_GET['month']) ? (int) $_GET['month'] : $currentMonth;
$selectedYear = isset($_GET['year']) ? (int) $_GET['year'] : $currentYear;

// Validate month range
if ($selectedMonth < 1 || $selectedMonth > 12) {
    $selectedMonth = $currentMonth;
}

$filter = $_GET["filter"] ?? "all";

// Get transactions based on filter and month
if($filter === "income")
    {
        $transactions = $db->getIncomeTransactionsByMonth($userId, $conn, $selectedMonth, $selectedYear);
    }
elseif($filter === "expense")
    {
        $transactions = $db->getExpenseTransactionsByMonth($userId, $conn, $selectedMonth, $selectedYear);
    }
else{
    $transactions = $db->getTransactionsByMonth($userId, $conn, $selectedMonth, $selectedYear);
}

// Get dashboard summary for cards (current month only)
$summary = $db->getDashboardSummary($userId, $conn);
$totalIncome = (float) ($summary['total_income'] ?? 0);
$totalExpense = (float) ($summary['total_expense'] ?? 0);
$totalBalance = $totalIncome - $totalExpense;

// Get month list for selector
$monthYearList = $db->getMonthYearList($userId, $conn);

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Transactions | SpendWise</title>

    <link rel="stylesheet" href="../css/transactions.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>
    <div class="dashboard">

    <aside class="sidebar">

        <div class="logo">

            <i class="fa-solid fa-wallet"></i>

            <span>SpendWise</span>

        </div>


        <nav class="nav">

            <a
                href="dashboard.php"
                class="nav-item"
            >
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>


            <a
                href="transactions.php"
                class="nav-item active"
            >
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                <span>Transactions</span>
            </a>


            <a
                href="budget.php"
                class="nav-item"
            >
                <i class="fa-solid fa-chart-pie"></i>
                <span>Budget</span>
            </a>


            <a
                href="reports.php"
                class="nav-item"
            >
                <i class="fa-solid fa-chart-column"></i>
                <span>Reports</span>
            </a>


            <a
                href="settings.php"
                class="nav-item"
            >
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>

        </nav>


        <div class="logout">

            <a
                href="../logout.php"
                class="nav-item"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>Logout</span>

            </a>

        </div>

    </aside>

    <!-- Main Content.. -->
    <main class="main-content">
        <header class="topbar">
        <h1>Transactions</h1>

                <p>
                    Manage your income and expenses
                </p>

        <div class="user-area">
           <div class="notification">
            <i class = "fa-regular fa-bell" ></i>
           </div>

           <div class="avatar">
            <?=strtoupper(substr($_SESSION["user_name"],0,1))?>
           </div>

           <span class="username">
            <?=htmlspecialchars($_SESSION["user_name"])?>
           </span>
           <span class="arrow">
            <i class="fa-solid fa-arrow-down"></i>
           </span>
        </div>
    </header>

     <!-- FINANCIAL SUMMARY -->
     <div class="summary-cards">
        <div class="summary-card summary-balance">
            <div class="card-title">Total Balance</div>
            <div class="amount">৳<?php echo number_format((float) $totalBalance, 2); ?></div>
        </div>

        <div class="summary-card summary-income">
            <div class="card-title">Total Income</div>
            <div class="amount">৳<?php echo number_format((float) $totalIncome, 2); ?></div>
        </div>

        <div class="summary-card summary-expense">
            <div class="card-title">Total Expense</div>
            <div class="amount">৳<?php echo number_format((float) $totalExpense, 2); ?></div>
        </div>
     </div>

     <!-- MONTH SELECTOR -->
     <div class="month-selector-container">
        <label for="monthYearSelect" class="month-selector-label">View Month:</label>
        <select id="monthYearSelect" class="month-selector">
            <option value="<?php echo date('Y-m'); ?>">Current Month (<?php echo date('F Y'); ?>)</option>
            <?php if (!empty($monthYearList)): ?>
                <?php foreach ($monthYearList as $record): ?>
                    <?php
                        $month = $record['month'];
                        $year = $record['year'];
                        $monthName = date('F', mktime(0, 0, 0, $month, 1));
                        $value = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
                    ?>
                    <option value="<?php echo $value; ?>"><?php echo $monthName . ' ' . $year; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
     </div>

     <script>
        document.getElementById('monthYearSelect').addEventListener('change', function() {
            const [year, month] = this.value.split('-');
            const filter = new URLSearchParams(window.location.search).get('filter') || 'all';
            window.location.href = 'transactions.php?month=' + month + '&year=' + year + '&filter=' + filter;
        });
     </script>

     <!-- TRANSACTIONS PAGE -->

        <section class="transactions-section">


            <!-- CONTROLS -->

            <div class="controls">

                <div class="filters">

                    <a href="transactions.php?filter=all&month=<?php echo $selectedMonth; ?>&year=<?php echo $selectedYear; ?>" class="filter-btn <?php echo ($filter === 'all') ? 'active' : ''; ?>">
                        All
                    </a>

                    <a href="transactions.php?filter=income&month=<?php echo $selectedMonth; ?>&year=<?php echo $selectedYear; ?>" class="filter-btn <?php echo ($filter === 'income') ? 'active' : ''; ?>">
                        Income
                    </a>

                    <a href="transactions.php?filter=expense&month=<?php echo $selectedMonth; ?>&year=<?php echo $selectedYear; ?>" class="filter-btn <?php echo ($filter === 'expense') ? 'active' : ''; ?>">
                        Expense
                    </a>

                </div>

                <button class="add-btn" id="openModal">
                    <i class="fa-solid fa-plus"></i>
                    Add Transaction
                </button>
            </div>
        
            <!-- TRANSACTIONS TABLE -->
            <div class="transaction-card">
                <div class="table-header">
                    <div>Description</div>
                    <div>Category</div>
                    <div>Type</div>
                    <div>Amount</div>
                    <div>Date</div>
                    <div>Action</div>
                </div>

                <div class="transaction-list">
                    <?php if(!empty($transactions)): ?>
                        <?php foreach($transactions as $transaction): ?>
                            <div class="transaction-row">
                                <div class="description">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span><?php echo htmlspecialchars($transaction['title']); ?></span>
                                </div>
                                <div><?php echo htmlspecialchars($transaction['category']); ?></div>
                                <div>
                                    <span class="type <?php echo ($transaction['type'] === 'income') ? 'income' : 'expense'; ?>">
                                        <?php echo ucfirst($transaction['type']); ?>
                                    </span>
                                </div>
                                <div class="amount <?php echo ($transaction['type'] === 'income') ? 'income' : 'expense'; ?>">
                                    <?php echo ($transaction['type'] === 'income') ? '+' : '-'; ?> ৳<?php echo number_format((float)$transaction['amount'], 2); ?>
                                </div>
                                <div><?php echo date('d M Y', strtotime($transaction['created_at'])); ?></div>
                                <div class="actions">
                                    <button class="edit-btn" onclick="editTransaction(<?php echo $transaction['id']; ?>)">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="delete-btn" onclick="deleteTransaction(<?php echo $transaction['id']; ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty">
                            <p>No transactions found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
   </section>
    </main>    
</div>
    

  <!-- TRANSACTION MODAL -->
   <div
    class="modal"
    id="transactionModal"
>


    <div class="modal-box">


        <div class="modal-title">


            <h2 id="modalTitle">

                Add Transaction

            </h2>


            <button
                type="button"
                id="closeModal"
                class="close-btn"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>


        </div>


        <form id="transactionForm">


            <input
                type="hidden"
                id="transactionId"
            >


            <div class="form-group">


                <label>
                    Description
                </label>


                <input
                    type="text"
                    id="title"
                    placeholder="e.g. Grocery Shopping"
                    required
                >


            </div>


            <div class="form-group">


                <label>
                    Amount
                </label>


                <input
                    type="number"
                    id="amount"
                    min="0.01"
                    step="0.01"
                    placeholder="0.00"
                    required
                >


            </div>


            <div class="form-group">


                <label>
                    Type
                </label>


                <select
                    id="type"
                    required
                >

                    <option value="">
                        Select Type
                    </option>

                    <option value="income">
                        Income
                    </option>

                    <option value="expense">
                        Expense
                    </option>

                </select>


            </div>


            <div class="form-group">


                <label>
                    Category
                </label>


                <select
                    id="category"
                    required
                >

                    <option value="">
                        Select Category
                    </option>

                    <option value="Food">
                        Food
                    </option>

                    <option value="Shopping">
                        Shopping
                    </option>

                    <option value="Transport">
                        Transport
                    </option>

                    <option value="Bills">
                        Bills
                    </option>

                    <option value="Entertainment">
                        Entertainment
                    </option>

                    <option value="Salary">
                        Salary
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>


            </div>


            <div class="modal-actions">


                <button
                    type="button"
                    class="cancel-btn"
                    id="cancelModal"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="save-btn"
                >

                    Save Transaction

                </button>


            </div>


        </form>


    </div>


</div>


<script>
// Handle Modal
const modal = document.getElementById('transactionModal');
const openModalBtn = document.getElementById('openModal');
const closeModalBtn = document.getElementById('closeModal');
const cancelModalBtn = document.getElementById('cancelModal');
const transactionForm = document.getElementById('transactionForm');

function openModal() {
    modal.classList.add('show');
}

function closeModal() {
    modal.classList.remove('show');
    transactionForm.reset();
}

openModalBtn.addEventListener('click', openModal);
closeModalBtn.addEventListener('click', closeModal);
cancelModalBtn.addEventListener('click', closeModal);

modal.addEventListener('click', function(e) {
    if (e.target === modal) closeModal();
});

// Handle Form Submission
transactionForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const id = document.getElementById('transactionId').value;
    const title = document.getElementById('title').value;
    const amount = document.getElementById('amount').value;
    const type = document.getElementById('type').value;
    const category = document.getElementById('category').value;

    const action = id ? 'update' : 'add';

    try {
        const response = await fetch('../controllers/transaction-process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                id: id || '',
                title: title,
                amount: amount,
                type: type,
                category: category
            })
        });

        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            closeModal();
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Something went wrong!');
    }
});

// Edit Transaction
async function editTransaction(id) {
    try {
        const response = await fetch('../controllers/transaction-process.php?filter=all');
        const data = await response.json();

        if (data.success) {
            const transaction = data.transactions.find(t => Number(t.id) === Number(id));
            
            if (transaction) {
                document.getElementById('transactionId').value = transaction.id;
                document.getElementById('title').value = transaction.title;
                document.getElementById('amount').value = transaction.amount;
                document.getElementById('type').value = transaction.type;
                document.getElementById('category').value = transaction.category;
                document.getElementById('modalTitle').textContent = 'Edit Transaction';
                openModal();
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Unable to load transaction.');
    }
}

// Delete Transaction
async function deleteTransaction(id) {
    if (!confirm('Are you sure you want to delete this transaction?')) {
        return;
    }

    try {
        const response = await fetch('../controllers/transaction-process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'delete',
                id: id
            })
        });

        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Unable to delete transaction.');
    }
}
</script>

<script src="transactions.js"></script>


</body>
