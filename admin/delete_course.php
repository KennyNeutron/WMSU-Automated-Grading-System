<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../classes/course_select.class.php';

if (isset($_POST['college_course_id'])) {
    $college_course_id = $_POST['college_course_id'];
    
    $course = new Course_curr();
    
    if ($course->delete($college_course_id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete Program']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Program ID missing']);
}
?>
