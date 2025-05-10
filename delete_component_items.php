<?php
require_once './classes/component_items.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true);

    $items_id = $_POST['items_id'] ?? null;

    if ($items_id === null) {
        error_log("Items ID is null in backend.");
        echo json_encode(['success' => false, 'message' => 'Invalid request. Items ID missing.']);
        exit;
    }
    if ($items_id) {
        $items = new ComponentItems();
        $result = $items->delete($items_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'sdfgsdfg']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete component item.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid request. Items ID: $items_id"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid HTTP method.']);
}

?>