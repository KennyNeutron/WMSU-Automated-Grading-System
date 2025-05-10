<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../classes/curr_year.class.php';

if (isset($_POST['curr_year_id'])) {
    $curr_year_id = $_POST['curr_year_id'];
    
    $curr_year = new Curr_year();
    
    if ($curr_year->delete($curr_year_id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete Curriculum']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Curriculum ID missing']);
}
?>
