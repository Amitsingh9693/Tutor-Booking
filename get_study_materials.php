<?php
require_once 'config.php';
checkLogin();

$student_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT sm.*, c.class_name 
        FROM study_materials sm
        JOIN classes c ON sm.class_id = c.class_id
        JOIN student_classes sc ON sm.class_id = sc.class_id
        WHERE sc.student_id = ?
        ORDER BY c.class_name, sm.upload_date DESC");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    
    $materials = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($materials);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => $e->getMessage()]);
}
?>