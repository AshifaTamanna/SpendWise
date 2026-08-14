<?php
require_once '../controllers/dashboard_process.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpendWise Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard-style.css">
    <style>
        .success-toast,
        .error-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
            padding: 14px 18px;
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
            display: none;
        }

        .success-toast {
            background: #20a65a;
        }

        .error-toast {
            background: #d9534f;
        }

        .success-toast.show,
        .error-toast.show {
            display: block;
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 64, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1500;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            width: min(500px, 90%);
            background: #fff;
            border-radius: 16px;
            padding: 30px 25px 20px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: #020640;
            margin: 0;
        }

        .close-modal {
            background: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6b7280;
        }

        .form-grid {
            display: grid;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-group label {
            font-size: 12px;
            color: #020640;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0 12px;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #020640;
            box-shadow: 0 0 0 2px rgba(2, 6, 64, 0.08);
        }

        .form-group input.error,
        .form-group select.error {
            border-color: #d9534f;
        }

        .error-message {
            color: #d9534f;
            font-size: 11px;
            display: none;
            min-height: 14px;
        }

        .error-message.show {
            display: block;
        }

        .modal-actions {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .cancel-btn,
        .submit-btn {
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }

        .cancel-btn {
            background: #e5e7eb;
            color: #374151;
        }

        .submit-btn {
            background: #020640;
            color: #fff;
        }

        .summary-cards {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php if (!empty($loginSuccess)): ?>
        <div class="success-toast show" id="loginToast"><?php echo htmlspecialchars($loginSuccess); ?></div>
    <?php endif; ?>

    <?php if (!empty($dashboardSuccess)): ?>
        <div class="success-toast show" id="dashboardToast"><?php echo htmlspecialchars($dashboardSuccess); ?></div>
    <?php endif; ?>

    <?php if (!empty($dashboardErrors)): ?>
        <div class="error-toast show" id="errorToast"><?php echo htmlspecialchars($dashboardErrors[0]); ?></div>
    <?php endif; ?>

    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo">
                <i class="fa-solid fa-wallet"></i>
                <span>SpendWise</span>
            </div>

            <nav class="nav">
                <a href="dashboard.php" class="nav-item active"><i class="fa-solid fa-house"></i><span>Dashboard</span></a>
                <a href="transactions.php" class="nav-item"><i class="fa-solid fa-arrow-right-arrow-left"></i><span>Transactions</span></a>
                <a href="budget.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i><span>Budget</span></a>
                <a href="reports.php" class="nav-item"><i class="fa-solid fa-chart-column"></i><span>Reports</span></a>
                <a href="settings.php" class="nav-item"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
            </nav>

            <div class="logout">
                <a href="../logout.php" class="nav-item">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="welcome">
                    <h2>Good morning, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>! 👋</h2>
                    <p>Here's your financial overview</p>
                </div>

                <div class="header-right">
                    <button class="notification" type="button"><i class="fa-regular fa-bell"></i></button>

                    <div class="profile">
                        <div class="profile-image"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?></div>
                        <div class="profile-info">
                            <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong>
                            <small>Premium</small>
                        </div>
                    </div>

                    <button class="add-btn" type="button" id="openTransactionModal">
                        <i class="fa-solid fa-plus"></i>
                        Add Transaction
                    </button>
                </div>
            </header>

            <!-- MONTH SELECTOR -->
            <div class="month-selector-container">
                <label for="monthYear" class="month-selector-label">View Month:</label>
                <select id="monthYear" class="month-selector">
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
                document.getElementById('monthYear').addEventListener('change', function() {
                    const [year, month] = this.value.split('-');
                    window.location.href = 'dashboard.php?month=' + month + '&year=' + year;
                });
            </script>

            <div class="summary-cards">
                <div class="summary-card">
                    <div class="card-title">Total Balance</div>
                    <div class="amount balance">৳<?php echo number_format((float) $totalBalance, 2); ?></div>
                    <div class="card-change positive"><i class="fa-solid fa-arrow-trend-up"></i> 12% from last month</div>
                </div>

                <div class="summary-card">
                    <div class="card-title">Total Income</div>
                    <div class="amount income">৳<?php echo number_format((float) $totalIncome, 2); ?></div>
                    <div class="card-change positive"><i class="fa-solid fa-arrow-trend-up"></i> 8% from last month</div>
                </div>

                <div class="summary-card">
                    <div class="card-title">Total Expense</div>
                    <div class="amount expense">৳<?php echo number_format((float) $totalExpense, 2); ?></div>
                    <div class="card-change negative"><i class="fa-solid fa-arrow-trend-up"></i> 15% from last month</div>
                </div>
            </div>

            <section class="dashboard-bottom">
                <div class="dashboard-box expense-overview">
                    <div class="box-header">
                        <h3>Expense Overview</h3>
                    </div>

                    <div class="expense-content">
                        <div class="donut">
                            <div class="donut-center">
                                <strong>৳<?php echo number_format((float) $totalExpense, 2); ?></strong>
                                <span>Total Expense</span>
                            </div>
                        </div>

                        <div class="expense-list">
                            <?php
                            $expenseBreakdown = $expenseBreakdown ?? [];
                            $totalExpenseLabel = $totalExpense > 0 ? $totalExpense : 1;
                            foreach ($expenseBreakdown as $item):
                                $percent = ($item['total_amount'] / $totalExpenseLabel) * 100;
                            ?>
                                <div class="expense-item">
                                    <div class="expense-name">
                                        <span class="dot" style="background: <?php echo '#' . substr(md5($item['category']), 0, 6); ?>;"></span>
                                        <?php echo htmlspecialchars($item['category']); ?>
                                    </div>
                                    <span>৳<?php echo number_format((float) $item['total_amount'], 2); ?> (<?php echo number_format($percent, 0); ?>%)</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="dashboard-box transactions-box">
                    <div class="box-header">
                        <h3>Recent Transactions</h3>
                        <a href="transactions.php">View All</a>
                    </div>

                    <div class="transaction-list">
                        <?php if (!empty($recentTransactions)): ?>
                            <?php foreach ($recentTransactions as $transaction): ?>
                                <div class="transaction">
                                    <div class="transaction-icon">
                                        <?php if ($transaction['type'] === 'income'): ?>
                                            <i class="fa-solid fa-arrow-down"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-arrow-up"></i>
                                        <?php endif; ?>
                                    </div>

                                    <div class="transaction-info">
                                        <strong><?php echo htmlspecialchars($transaction['title']); ?></strong>
                                        <span><?php echo htmlspecialchars($transaction['category']); ?></span>
                                    </div>

                                    <div class="transaction-right">
                                        <strong class="<?php echo $transaction['type'] === 'income' ? 'green' : 'red'; ?>">
                                            <?php echo $transaction['type'] === 'income' ? '+৳' : '-৳'; ?><?php echo number_format((float) $transaction['amount'], 2); ?>
                                        </strong>
                                        <span><?php echo date('d M Y', strtotime($transaction['created_at'])); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No transactions yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <div class="modal" id="transactionModal" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Transaction</h3>
                <button type="button" class="close-modal" id="closeTransactionModal" aria-label="Close form">×</button>
            </div>

            <form action="../controllers/dashboard_process.php" method="POST" id="transactionForm" novalidate>
                <input type="hidden" name="add_transaction" value="1">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" placeholder="e.g. Salary, Rent, Groceries">
                        <div class="error-message" id="titleError"></div>
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" step="0.01" min="0.01" id="amount" name="amount" placeholder="0.00">
                        <div class="error-message" id="amountError"></div>
                    </div>

                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" name="type">
                            <option value="">Select type</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                        <div class="error-message" id="typeError"></div>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" id="category" name="category" placeholder="Food, Salary, Transport, Bills">
                        <div class="error-message" id="categoryError"></div>
                    </div>

                    <div class="form-group">
                        <label for="transaction_date">Date</label>
                        <input type="date" id="transaction_date" name="transaction_date" value="<?php echo date('Y-m-d'); ?>">
                        <div class="error-message" id="dateError"></div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" id="cancelTransactionBtn">Cancel</button>
                    <button type="submit" class="submit-btn">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const transactionModal = document.getElementById('transactionModal');
        const openTransactionModalBtn = document.getElementById('openTransactionModal');
        const closeTransactionModalBtn = document.getElementById('closeTransactionModal');
        const cancelTransactionBtn = document.getElementById('cancelTransactionBtn');
        const transactionForm = document.getElementById('transactionForm');

        function openModal() {
            transactionModal.classList.add('show');
            transactionModal.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            transactionModal.classList.remove('show');
            transactionModal.setAttribute('aria-hidden', 'true');
            transactionForm.reset();
            clearAllErrors();
        }

        function setError(fieldId, message) {
            const input = document.getElementById(fieldId);
            const errorBox = document.getElementById(fieldId + 'Error');

            if (input) input.classList.add('error');
            if (errorBox) {
                errorBox.textContent = message;
                errorBox.classList.add('show');
            }
        }

        function clearError(fieldId) {
            const input = document.getElementById(fieldId);
            const errorBox = document.getElementById(fieldId + 'Error');

            if (input) input.classList.remove('error');
            if (errorBox) {
                errorBox.textContent = '';
                errorBox.classList.remove('show');
            }
        }

        function clearAllErrors() {
            ['title', 'amount', 'type', 'category'].forEach(clearError);
        }

        function validateTitle() {
            const value = document.getElementById('title').value.trim();
            if (!value) {
                setError('title', 'Title is required.');
                return false;
            }
            if (value.length < 2) {
                setError('title', 'Title must be at least 2 characters.');
                return false;
            }
            clearError('title');
            return true;
        }

        function validateAmount() {
            const value = document.getElementById('amount').value.trim();
            if (!value || Number(value) <= 0 || Number.isNaN(Number(value))) {
                setError('amount', 'Amount must be a valid number greater than 0.');
                return false;
            }
            clearError('amount');
            return true;
        }

        function validateType() {
            const value = document.getElementById('type').value;
            if (!value) {
                setError('type', 'Please select a transaction type.');
                return false;
            }
            clearError('type');
            return true;
        }

        function validateCategory() {
            const value = document.getElementById('category').value.trim();
            if (!value) {
                setError('category', 'Category is required.');
                return false;
            }
            clearError('category');
            return true;
        }

        openTransactionModalBtn.addEventListener('click', openModal);
        closeTransactionModalBtn.addEventListener('click', closeModal);
        cancelTransactionBtn.addEventListener('click', closeModal);

        document.getElementById('title').addEventListener('input', validateTitle);
        document.getElementById('amount').addEventListener('input', validateAmount);
        document.getElementById('type').addEventListener('change', validateType);
        document.getElementById('category').addEventListener('input', validateCategory);

        transactionForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const isTitleValid = validateTitle();
            const isAmountValid = validateAmount();
            const isTypeValid = validateType();
            const isCategoryValid = validateCategory();

            if (isTitleValid && isAmountValid && isTypeValid && isCategoryValid) {
                transactionForm.submit();
            }
        });

        setTimeout(function () {
            const loginToast = document.getElementById('loginToast');
            const dashboardToast = document.getElementById('dashboardToast');
            const errorToast = document.getElementById('errorToast');

            [loginToast, dashboardToast, errorToast].forEach(function (toast) {
                if (toast) {
                    toast.classList.remove('show');
                }
            });
        }, 2500);
    </script>
</body>
</html>

