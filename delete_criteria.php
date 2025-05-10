<?php
require_once './classes/component.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true);

    $component_id = $_POST['component_id'] ?? null;

    if ($component_id === null) {
        error_log("Component ID is null in backend.");
        echo json_encode(['success' => false, 'message' => 'Invalid request. Component ID missing.']);
        exit;
    }
    if ($component_id) {
        $component = new SubjectComponents();
        $result = $component->delete($component_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => '']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete component.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid request. Component ID: $component_id"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid HTTP method.']);
}

?>