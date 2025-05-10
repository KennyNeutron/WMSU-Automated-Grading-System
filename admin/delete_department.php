<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../classes/department.class.php';

if (isset($_POST['department_id'])) {
    $department_id = $_POST['department_id'];
    
    $department = new Department();
    
    if ($department->delete($department_id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete department']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Department ID missing']);
}
?>
