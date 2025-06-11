<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_email"]) || !isset($_GET["sender"])) {
    exit();
}

$receiver = $_SESSION["user_email"];
$sender = $_GET["sender"];

// Mark messages as read
$stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_email = ? AND receiver_email = ? AND is_read = 0");
$stmt->bind_param("ss", $sender, $receiver);
$stmt->execute();
$stmt->close();
exit();
?>
