<?php
include "session_start.php";
include "db.php"; 

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["id"]) || empty($_POST["id"])) {
        echo json_encode(["status" => "error", "message" => "Message ID is missing!"]);
        exit();
    }

    if (!isset($_SESSION["user_email"])) {
        echo json_encode(["status" => "error", "message" => "User not authenticated!"]);
        exit();
    }

    $messageId = intval($_POST["id"]); // Sanitize input
    $userEmail = $_SESSION["user_email"]; // Get the logged-in user

    // Check if the message exists and belongs to the user
    $checkQuery = "SELECT id FROM messages WHERE id = ? AND sender_email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("is", $messageId, $userEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Message not found or unauthorized deletion!"]);
        exit();
    }

    $stmt->close();

    // Proceed to delete the message
    $deleteQuery = "DELETE FROM messages WHERE id = ? AND sender_email = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("is", $messageId, $userEmail);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "id" => $messageId, "message" => "Message deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error deleting message"]);
    }

    $stmt->close();
    $conn->close();
}
?>
