<?php

session_start();

include '../models/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $terms = isset($_POST["terms"]);

    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required";
    } elseif (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }

    if (empty($confirm_password)) {
        $errors[] = "Confirm password is required";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    if (!$terms) {
        $errors[] = "You must agree to the Terms & Conditions";
    }

    if (empty($errors)) {

        $mydb = new MyDB();
        $conn = $mydb->createConn();

        $result = $mydb->searchUser($email, $conn);

        if ($result->num_rows > 0) {

            $errors[] = "Email already exists. Please use a different email.";

        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $result = $mydb->createUser(
                $name,
                $email,
                $hashedPassword,
                $conn
            );

            if ($result === true) {

                $conn->close();

                $_SESSION['success'] = "Registration successful!";

                header("Location: ../views/register-view.php");
                exit;

            } else {

                $errors[] = "Registration failed: " . $conn->error;
            }
        }

        $conn->close();
    }

    if (!empty($errors)) {

        $_SESSION['errors'] = $errors;
        $_SESSION['old_name'] = $name;
        $_SESSION['old_email'] = $email;

        header("Location: ../views/register-view.php");
        exit;
    }
}

// Do not leave a blank page if this file is opened directly.
header("Location: ../views/register-view.php");
exit;

?>
