<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../classes/college.class.php';

if (isset($_POST['college_id'])) {
    $college_id = $_POST['college_id'];
    
    $college = new College();
    
    if ($college->delete($college_id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete college']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'College ID missing']);
}
?>
