<?php
require "config.php";

function dbConnect(){
    $mysqli =new mysqli(SERVER,USERNAME,PASSWORD,DATABASE);
    if($mysqli -> connect_errno != 0){
        return FALSE;
    }
    else{
        return $mysqli;
    }
}





function getStData($regno) {
    $mysqli = dbConnect(); 
    if (!$mysqli) {
        die("Database connection failed.");
    }

    $stmt = $mysqli->prepare("SELECT * FROM st1 WHERE regno = ?");
    if (!$stmt) {
        die("Prepare statement failed: " . $mysqli->error);
    }

    $stmt->bind_param("s", $regno);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc(); 

}


function getTeacherData($t_id) {
    $conn = new PDO("mysql:host=localhost;dbname=depthub", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("
        SELECT t.*, d.dept_name 
        FROM teachers t
        LEFT JOIN departments d ON t.department = d.dept_id
        WHERE t.T_id = ?
    ");
    $stmt->execute([$t_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}