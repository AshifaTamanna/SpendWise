<?php

class MyDB
{
    function createConn()
    {
        $conn = new mysqli("localhost", "root", "", "spendwise");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }

    function searchUser($email, $conn)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return $stmt->get_result();
    }

    function createUser($name, $email, $password, $conn)
    {
        $sql = "INSERT INTO users (name, email, password, created_at)
                VALUES (?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $password);

        return $stmt->execute();
    }

    function getDashboardSummary($userId, $conn)
    {
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income,
                    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense
                FROM transactions
                WHERE user_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    function getRecentTransactions($userId, $conn, $limit = 5)
    {
        $sql = "SELECT *
                FROM transactions
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function getExpenseBreakdown($userId, $conn)
    {
        $sql = "SELECT category, SUM(amount) AS total_amount
                FROM transactions
                WHERE user_id = ? AND type = 'expense'
                GROUP BY category
                ORDER BY total_amount DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function addTransaction($userId, $title, $amount, $type, $category, $conn)
    {
        $sql = "INSERT INTO transactions (user_id, title, amount, type, category, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdss", $userId, $title, $amount, $type, $category);

        return $stmt->execute();
    }
}
?>