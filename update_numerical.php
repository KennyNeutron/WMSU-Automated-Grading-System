<?php
session_start();
require_once './classes/point_equivalent.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pointEqv = new PointEqv();
    
    $faculty_sub_id = $_POST['faculty_sub_id'] ?? null;
    $point_eqv_id = $_POST['point_eqv_id'] ?? null;
    $grade_key = $_POST['grade_key'] ?? null;
    $numerical_rating = $_POST['numerical_rating'] ?? null;
    
    if ($faculty_sub_id && $point_eqv_id && $grade_key && $numerical_rating) {
        // Update the numerical rating in the database
        $success = $pointEqv->updateNumericalRating($point_eqv_id, $grade_key, $numerical_rating);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Numerical rating updated successfully' : 'Failed to update numerical rating'
        ]);
        exit();
    }
}

header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Invalid request'
]);
exit();
?>