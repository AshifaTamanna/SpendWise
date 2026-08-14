<?php

session_start();

header("Content-Type: application/json");

require_once "../models/mydb.php";

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);

    exit;
}

$db = new MyDB();

$conn = $db->createConn();

$userId = $_SESSION["user_id"];

$method = $_SERVER["REQUEST_METHOD"];

if($method === "GET")
    {
        $_filter = $_GET["filter"] ?? "all";
        if($_filter === "income")
            {
                $transactions = $db -> getIncomeTransactions($userId,$conn);
            }
        elseif ($_filter === "expense") {
            $transactions = $db->getExpenseTransactions($userId,$conn);
        } else {
            $transactions = $db -> getAllTransactions($userId,$conn);
        }
        echo json_encode([
        "success" => true,
        "transactions" => $transactions
    ]);

    $conn->close();
    exit;
    }
if($method === "POST")
    {
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData,true);
    if (!is_array($data)) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid JSON data"
        ]);

        $conn->close();

        exit;
    }
$action = $data["action"] ?? "";

 if ($action === "add") {

        $title = trim(
            $data["title"] ?? ""
        );

        $amount = $data["amount"] ?? "";

        $type = $data["type"] ?? "";

        $category = trim(
            $data["category"] ?? ""
        );

        $transaction_date = $data["date"] ?? date('Y-m-d');


        if (
            $title === "" ||
            $amount === "" ||
            $type === "" ||
            $category === ""
        ) {

            echo json_encode([
                "success" => false,
                "message" => "All fields are required"
            ]);

            $conn->close();

            exit;
        }


        if (
            !is_numeric($amount) ||
            $amount <= 0
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Invalid amount"
            ]);

            $conn->close();

            exit;
        }


        if (
            $type !== "income" &&
            $type !== "expense"
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Invalid transaction type"
            ]);

            $conn->close();

            exit;
        }


        $result = $db->addTransaction(
            $userId,
            $title,
            $amount,
            $type,
            $category,
            $conn,
            $transaction_date
        );


        echo json_encode([
            "success" => $result,
            "message" => $result
                ? "Transaction added successfully"
                : "Failed to add transaction"
        ]);

        $conn->close();

        exit;
    }

     if ($action === "delete") {

        $id = $data["id"] ?? 0;


        if (
            !is_numeric($id) ||
            $id <= 0
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Invalid transaction ID"
            ]);

            $conn->close();

            exit;
        }


        $result =
            $db->deleteTransaction(
                $id,
                $userId,
                $conn
            );


        echo json_encode([
            "success" => $result,
            "message" => $result
                ? "Transaction deleted successfully"
                : "Failed to delete transaction"
        ]);

        $conn->close();

        exit;
    }
if ($action === "update") {

        $id = $data["id"] ?? 0;

        $title = trim(
            $data["title"] ?? ""
        );

        $amount =
            $data["amount"] ?? "";

        $type =
            $data["type"] ?? "";

        $category = trim(
            $data["category"] ?? ""
        );


        if (
            !is_numeric($id) ||
            $id <= 0 ||
            $title === "" ||
            $amount === "" ||
            $type === "" ||
            $category === ""
        ) {

            echo json_encode([
                "success" => false,
                "message" => "All fields are required"
            ]);

            $conn->close();

            exit;
        }


        if (
            !is_numeric($amount) ||
            $amount <= 0
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Invalid amount"
            ]);

            $conn->close();

            exit;
        }


        if (
            $type !== "income" &&
            $type !== "expense"
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Invalid transaction type"
            ]);

            $conn->close();

            exit;
        }


        $result =
            $db->updateTransaction(
                $id,
                $userId,
                $title,
                $amount,
                $type,
                $category,
                $conn
            );


        echo json_encode([
            "success" => $result,
            "message" => $result
                ? "Transaction updated successfully"
                : "Failed to update transaction"
        ]);

        $conn->close();

        exit;
    }


    echo json_encode([
        "success" => false,
        "message" => "Invalid action"
    ]);

    $conn->close();

    exit;
}


echo json_encode([
    "success" => false,
    "message" => "Invalid request method"
]);

$conn->close();

?>