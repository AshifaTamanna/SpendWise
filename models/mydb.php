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

    function getDashboardSummary($userId, $conn, $month = null, $year = null)
    {
        if ($month === null) $month = date('m');
        if ($year === null) $year = date('Y');

        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income,
                    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense
                FROM transactions
                WHERE user_id = ?
                AND MONTH(created_at) = ?
                AND YEAR(created_at) = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $month, $year);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    function getRecentTransactions($userId, $conn, $limit = 5, $month = null, $year = null)
    {
        if ($month === null) $month = date('m');
        if ($year === null) $year = date('Y');

        $sql = "SELECT *
                FROM transactions
                WHERE user_id = ?
                AND MONTH(created_at) = ?
                AND YEAR(created_at) = ?
                ORDER BY created_at DESC
                LIMIT ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $userId, $month, $year, $limit);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function getExpenseBreakdown($userId, $conn, $month = null, $year = null)
    {
        if ($month === null) $month = date('m');
        if ($year === null) $year = date('Y');

        $sql = "SELECT category, SUM(amount) AS total_amount
                FROM transactions
                WHERE user_id = ?
                AND type = 'expense'
                AND MONTH(created_at) = ?
                AND YEAR(created_at) = ?
                GROUP BY category
                ORDER BY total_amount DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $month, $year);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function addTransaction($userId, $title, $amount, $type, $category, $conn, $transactionDate = null)
    {
        if ($transactionDate === null) {
            $transactionDate = date('Y-m-d H:i:s');
        } else {
            // Ensure the date is in proper format
            $transactionDate = date('Y-m-d H:i:s', strtotime($transactionDate));
        }

        $sql = "INSERT INTO transactions (user_id, title, amount, type, category, created_at)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdsss", $userId, $title, $amount, $type, $category, $transactionDate);

        return $stmt->execute();
    }
    function getAllTransactions($userId, $conn)
    {
       $sql = "SELECT * FROM transactions WHERE user_id = ? 
                                                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    function getIncomeTransactions($userId,$conn)
    {

        $sql = "SELECT *
                FROM transactions
                WHERE user_id = ?
                AND type = 'income'
                ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function getExpenseTransactions($userId, $conn)
    {
        $sql = "SELECT *
                FROM transactions
                WHERE user_id = ?
                AND type = 'expense'
                ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    function getTransactionById($id, $userId, $conn)
    {
        $sql = "SELECT *
                FROM transactions
                WHERE id = ?
                AND user_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc(); //fetching one row of associative array
    }
    function updateTransaction($id, $userId, $title, $amount,$type, $category,$conn)
    {
        $sql = "UPDATE transactions SET title = ? , amount= ? ,type = ?,
                    category = ? WHERE id=? AND user_id = ?";
        $stmt = $conn->prepare($sql);
          $stmt->bind_param(
            "sdssi",
            $title,
            $amount,
            $type,
            $category,
            $id,
            $userId
        );

        return $stmt->execute();

    }

    function deleteTransaction($id, $userId, $conn)
    {
        $sql = "DELETE FROM transactions
                WHERE id = ?
                AND user_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ii",
            $id,
            $userId
        );

        return $stmt->execute();
    }
    function countTransactions($userId, $conn)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM transactions
                WHERE user_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    function getMonthYearList($userId, $conn)
    {
        $sql = "SELECT DISTINCT YEAR(created_at) as year, MONTH(created_at) as month
                FROM transactions
                WHERE user_id = ?
                ORDER BY year DESC, month DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function getTransactionsByMonth($userId, $conn, $month, $year)
    {
        $sql = "SELECT *
                FROM transactions
                WHERE user_id = ?
                AND MONTH(created_at) = ?
                AND YEAR(created_at) = ?
                ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $month, $year);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function getIncomeTransactionsByMonth($userId, $conn, $month, $year)
    {
        $sql = "SELECT *
                FROM transactions
                WHERE user_id = ?
                AND type = 'income'
                AND MONTH(created_at) = ?
                AND YEAR(created_at) = ?
                ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $month, $year);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function getExpenseTransactionsByMonth($userId, $conn, $month, $year)
    {
        $sql = "SELECT *
                FROM transactions
                WHERE user_id = ?
                AND type = 'expense'
                AND MONTH(created_at) = ?
                AND YEAR(created_at) = ?
                ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $month, $year);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

}
?>