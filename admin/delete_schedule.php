<?php
require_once '../classes/faculty_sched.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true);

    $sched_id = $input['sched_id'] ?? null;

    if ($sched_id === null) {
        error_log("Sched ID is null in backend.");
        echo json_encode(['success' => false, 'message' => 'Invalid request. Sched ID missing.']);
        exit;
    }
    if ($sched_id) {
        $sched = new Faculty_Sched();
        $result = $sched->delete($sched_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => '']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete schedule.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid request. Sched ID: $sched_id"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid HTTP method.']);
}

?>