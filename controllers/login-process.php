<?php

session_start();

include '../models/mydb.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$errors = [];

if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_email'] = $email;
    header('Location: ../views/login.php');
    exit;
}

$mydb = new MyDB();
$conn = $mydb->createConn();
$result = $mydb->searchUser($email, $conn);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['login_success'] = 'Login successful!';

        $conn->close();
        header('Location: ../views/dashboard.php');
        exit;
    }
}

$conn->close();
$_SESSION['errors'] = ['Invalid email or password.'];
$_SESSION['old_email'] = $email;
header('Location: ../views/login.php');
exit;
