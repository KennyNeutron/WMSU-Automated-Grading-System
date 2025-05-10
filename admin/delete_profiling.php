<?php
require_once '../classes/profiling.class.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true);

    $profiling_id = $input['profiling_id'] ?? null;

    if ($profiling_id === null) {
        error_log("Profiling ID is null in backend.");
        echo json_encode(['success' => false, 'message' => 'Invalid request. Profiling ID missing.']);
        exit;
    }
    if ($profiling_id) {
        $profiling = new Profiling();
        $result = $profiling->delete($profiling_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => '']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete faculty.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid request. Profiling ID: $profiling_id"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid HTTP method.']);
}

?>