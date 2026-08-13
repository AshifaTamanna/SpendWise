<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$transactionErrors = $_SESSION['transaction_errors'] ?? [];
unset($_SESSION['transaction_errors']);

$transactionSuccess = $_SESSION['transaction_success'] ?? '';
unset($_SESSION['transaction_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transaction</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/add_transaction.css">
    <style>
        .error-message-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 18px;
        }

        .success-message-box {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo">
                <i class="fa-solid fa-wallet"></i>
                <span>SpendWise</span>
            </div>

            <nav class="nav">
                <a href="dashboard.php" class="nav-item">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>

                <a href="add-transaction.php" class="nav-item active">
                    <i class="fa-solid fa-wallet"></i>
                    <span>Wallet</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    <span>Transactions</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>Reports</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>
            </nav>

            <div class="logout">
                <a href="login.php" class="nav-item">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <main class="main-content">
        <div class="transaction-page">
            <div class="page-header">
                <a href="dashboard.php" class="back-button">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>

                <div>
                    <h2>Add Transaction</h2>
                    <p>Record your income or expense</p>
                </div>
            </div>

            <?php if (!empty($transactionErrors)): ?>
                <div class="error-message-box">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($transactionErrors[0]); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($transactionSuccess)): ?>
                <div class="success-message-box">
                    <i class="fa-solid fa-circle-check"></i>
                    <?php echo htmlspecialchars($transactionSuccess); ?>
                </div>
            <?php endif; ?>

            <div class="transaction-container">
                <div class="form-section">
                    <form method="POST" action="../controllers/add_transaction_process.php" id="transactionForm">
                        <div class="form-group">
                            <label>Type</label>
                            <div class="type-options">
                                <label class="radio-option">
                                    <input type="radio" name="type" value="income">
                                    <span class="custom-radio"></span>
                                    Income
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="type" value="expense">
                                    <span class="custom-radio"></span>
                                    Expense
                                </label>
                            </div>
                            <div class="error-message" id="typeError"></div>
                        </div>

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" placeholder="Enter title">
                            <div class="error-message" id="titleError"></div>
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <div class="amount-input">
                                <span>৳</span>
                                <input type="number" id="amount" name="amount" placeholder="Enter amount" step="0.01" min="0.01">
                            </div>
                            <div class="error-message" id="amountError"></div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category" name="category">
                                    <option value="">Select category</option>
                                    <option value="Food">Food</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Shopping">Shopping</option>
                                    <option value="Bills">Bills</option>
                                    <option value="Entertainment">Entertainment</option>
                                    <option value="Education">Education</option>
                                    <option value="Health">Health</option>
                                    <option value="Salary">Salary</option>
                                    <option value="Freelance">Freelance</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="error-message" id="categoryError"></div>
                            </div>

                            <div class="form-group">
                                <label for="transaction_date">Date</label>
                                <input type="date" id="transaction_date" name="transaction_date">
                                <div class="error-message" id="dateError"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="note">Note <span>(optional)</span></label>
                            <textarea id="note" name="note" placeholder="Enter note" rows="4" maxlength="500"></textarea>
                            <div class="error-message" id="noteError"></div>
                        </div>

                        <input type="hidden" name="add_transaction" value="1">

                        <div class="form-buttons">
                            <a href="dashboard.php" class="cancel-btn">Cancel</a>
                            <button type="submit" class="submit-btn">
                                <i class="fa-solid fa-plus"></i>
                                Add Transaction
                            </button>
                        </div>
                    </form>
                </div>

                <div class="illustration-section">
                    <div class="money-illustration">
                        <i class="fa-solid fa-coins coin coin-one"></i>
                        <i class="fa-solid fa-coins coin coin-two"></i>
                        <i class="fa-solid fa-coins coin coin-three"></i>
                        <div class="wallet-illustration">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>

                    <h3>Track your spending</h3>
                    <p>Keep your finances organized by recording every transaction.</p>
                </div>
            </div>
        </div>
        </main>
    </div>

</body>
</html>
