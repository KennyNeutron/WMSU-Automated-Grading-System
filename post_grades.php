<?php
session_start();
require_once './classes/posted_grades.class.php';
require_once './classes/grades.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit();
    }

    if (!isset($data['faculty_sub_id']) || !isset($data['emp_id']) || !isset($data['students'])) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit();
    }

    $faculty_sub_id = $data['faculty_sub_id'];
    $emp_id = $data['emp_id'];
    $students = $data['students'];

    $postedGrades = new PostedGrades();
    $results = [];
    $allSuccess = true;

    foreach ($students as $student) {
        if (!isset($student['student_data_id']) || !isset($student['point_eqv'])) {
            $results[] = [
                'student_data_id' => $student['student_data_id'] ?? 'unknown',
                'success' => false,
                'message' => 'Missing student data'
            ];
            $allSuccess = false;
            continue;
        }

        try {
            $postedGrades->emp_id = $emp_id;
            $postedGrades->student_data_id = $student['student_data_id'];
            $postedGrades->faculty_sub_id = $faculty_sub_id;
            $postedGrades->point_eqv = $student['point_eqv'];

            if ($postedGrades->add()) {
                $results[] = [
                    'student_data_id' => $student['student_data_id'],
                    'success' => true,
                    'message' => 'Grade posted successfully'
                ];
            } else {
                $results[] = [
                    'student_data_id' => $student['student_data_id'],
                    'success' => false,
                    'message' => 'Failed to save grade'
                ];
                $allSuccess = false;
            }
        } catch (Exception $e) {
            $results[] = [
                'student_data_id' => $student['student_data_id'],
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
            $allSuccess = false;
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => $allSuccess,
        'results' => $results
    ]);
    exit();
}

header('HTTP/1.1 400 Bad Request');
echo json_encode(['success' => false, 'error' => 'Invalid request method']);
?>