<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit;
}

include '../models/mydb.php';

$db = new MyDB();
$conn = $db->createConn();
$userId = (int) $_SESSION['user_id'];

$loginSuccess = $_SESSION['login_success'] ?? '';
unset($_SESSION['login_success']);

$dashboardSuccess = '';
$dashboardErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_transaction'])) {
    $title = trim($_POST['title'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if ($title === '') {
        $dashboardErrors[] = 'Title is required.';
    } elseif (strlen($title) < 2) {
        $dashboardErrors[] = 'Title must be at least 2 characters long.';
    }

    if ($amount === '' || !is_numeric($amount) || (float) $amount <= 0) {
        $dashboardErrors[] = 'Amount must be a valid number greater than 0.';
    }

    if ($type !== 'income' && $type !== 'expense') {
        $dashboardErrors[] = 'Please select a valid transaction type.';
    }

    if ($category === '') {
        $dashboardErrors[] = 'Category is required.';
    }

    if (empty($dashboardErrors)) {
        $inserted = $db->addTransaction($userId, $title, (float) $amount, $type, $category, $conn);

        if ($inserted) {
            $dashboardSuccess = 'Transaction added successfully.';
            header('Location: ../views/dashboard.php');
            exit;
        }

        $dashboardErrors[] = 'Unable to save the transaction. Please try again.';
    }
}

$summary = $db->getDashboardSummary($userId, $conn);
$totalIncome = (float) ($summary['total_income'] ?? 0);
$totalExpense = (float) ($summary['total_expense'] ?? 0);
$totalBalance = $totalIncome - $totalExpense;

$recentTransactions = $db->getRecentTransactions($userId, $conn, 5);
$expenseBreakdown = $db->getExpenseBreakdown($userId, $conn);

$conn->close();
