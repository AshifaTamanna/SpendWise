<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transaction</title>
</head>
<body>
    <div class="sidebar">
       
    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-wallet"></i>
            <span>SpendWise</span>
        </div>

        <nav class="nav">
            <a href="dashboard.php" class="nav-item active"></a>
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span>

            <a href="add-transaction.php" class="nav-item active"></a>
            <i class="fa-solid fa-wallet"></i>
            <span>Wallet</span>

            <a href="#" class="nav-item"></a>
            <i class="fa-solid fa-arrow-right-arrow-left"></i>
            <span>Transactions</span>
            
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
            <a href="logout.php" class="nav-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>

        </div>

    </aside>
    <main class="main-content">
        <div class="transactione-page">
            <div class="page-header">

                <a href="dashboard.php" class="back-button">

                    <i class="fa-solid fa-arrow-left"></i>

                </a>


                <div>

                    <h2>Add Transaction</h2>

                    <p>Record your income or expense</p>
                </div>
            </div>
            <?php if(!empty($error)): ?>
                <div class="error-message">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
                 <?php endif; ?>
                 


        <div class="transaction-container">
            <div class="form-selection">
                <form method="POST" action="" id="transactionForm">
                    <div class="form-group">
                        <label>Type</label>
                        <div class="type-option">
                            <label class="radio-option">
                                <input type="radio" name="type" value="income" >
                                <span class="custom-radio"></span>
                                Income
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="type" value="Expense">
                                <span class="custom-radio"></span>
                                Expense
                            </label>

                        </div>
                    </div>
                      <div class="form-group">

                            <label for="title">

                                Title

                            </label>


                            <input type="text" id="title" name="title" placeholder="Enter title">

                         <div class="form-group">

                            <label for="amount">

                                Amount

                            </label>


                            <div class="amount-input">

                                <span>৳</span>

                                <input
                                    type="number"
                                    id="amount"
                                    name="amount"
                                    placeholder="Enter amount"
                                >

                            </div>

                        </div>
                        <div class="form-row">


                            <div class="form-group">

                                <label for="category">

                                    Category

                                </label>


                                <select
                                    id="category"
                                    name="category"
                                    
                                >

                                    <option value="">
                                        Select category
                                    </option>

                                    <option value="Food">
                                        Food
                                    </option>

                                    <option value="Transport">
                                        Transport
                                    </option>

                                    <option value="Shopping">
                                        Shopping
                                    </option>

                                    <option value="Bills">
                                        Bills
                                    </option>

                                    <option value="Entertainment">
                                        Entertainment
                                    </option>

                                    <option value="Education">
                                        Education
                                    </option>

                                    <option value="Health">
                                        Health
                                    </option>

                                    <option value="Salary">
                                        Salary
                                    </option>

                                    <option value="Freelance">
                                        Freelance
                                    </option>

                                    <option value="Other">
                                        Other
                                    </option>

                                </select>

                            </div>
                <div class="form-group">

                                <label for="transaction_date">

                                    Date

                                </label>


                                <input
                                    type="date"
                                    id="transaction_date"
                                    name="transaction_date"
                                    
                                >

                            </div>
                        </div>
                                                <div class="form-group">

                            <label for="note">

                                Note <span>(optional)</span>

                            </label>


                            <textarea
                                id="note"
                                name="note"
                                placeholder="Enter note"
                                rows="4"
                                maxlength="500"
                            ></textarea>

                        </div>
                        <div class="form-buttons">


                            <a
                                href="dashboard.php"
                                class="cancel-btn"
                            >

                                Cancel

                            </a>


                            <button
                                type="submit"
                                name="add_transaction"
                                class="submit-btn"
                            >

                                <i class="fa-solid fa-plus"></i>

                                Add Transaction

                            </button>


                        </div>


                        </div>
                </form>
            </div>
        
                <!-- RIGHT SIDE -->

                <div class="illustration-section">

                    <div class="money-illustration">

                        <i class="fa-solid fa-coins coin coin-one"></i>

                        <i class="fa-solid fa-coins coin coin-two"></i>

                        <i class="fa-solid fa-coins coin coin-three"></i>

                        <div class="wallet-illustration">

                            <i class="fa-solid fa-wallet"></i>

                        </div>

                    </div>


                    <h3>

                        Track your spending

                    </h3>


                    <p>

                        Keep your finances organized by
                        recording every transaction.

                    </p>

                </div>


            </div>


        </div>

    </main>
    </div>
    <script src="add_transaction.js"></script>
    
</body>
</html>