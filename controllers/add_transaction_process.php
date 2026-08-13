<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit;
}

require_once '../models/mydb.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['add_transaction'])) {
    header('Location: ../views/dashboard.php');
    exit;
}

$db = new MyDB();
$conn = $db->createConn();
$userId = (int) $_SESSION['user_id'];

$title = trim($_POST['title'] ?? '');
$amount = trim($_POST['amount'] ?? '');
$type = trim($_POST['type'] ?? '');
$category = trim($_POST['category'] ?? '');
$errors = [];

if ($title === '') {
    $errors[] = 'Title is required.';
} elseif (strlen($title) < 2) {
    $errors[] = 'Title must be at least 2 characters long.';
}

if ($amount === '' || !is_numeric($amount) || (float) $amount <= 0) {
    $errors[] = 'Amount must be a valid number greater than 0.';
}

if ($type !== 'income' && $type !== 'expense') {
    $errors[] = 'Please select a transaction type.';
}

if ($category === '') {
    $errors[] = 'Category is required.';
}

if (!empty($errors)) {
    $_SESSION['transaction_errors'] = $errors;
    $conn->close();
    header('Location: ../views/add-transaction.php');
    exit;
}

$inserted = $db->addTransaction($userId, $title, (float) $amount, $type, $category, $conn);
$conn->close();

if ($inserted) {
    $_SESSION['transaction_success'] = 'Transaction added successfully.';
    header('Location: ../views/dashboard.php');
    exit;
}

$_SESSION['transaction_errors'] = ['Unable to save the transaction. Please try again.'];
header('Location: ../views/add-transaction.php');
exit;
