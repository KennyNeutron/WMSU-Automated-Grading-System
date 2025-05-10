<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../vendor/autoload.php';

require_once '../tools/functions.php';
require_once '../classes/students.class.php';
require_once '../classes/grades.class.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excelFile'])) {
    $response = ['success' => false, 'message' => ''];

    try {
        $faculty_sub_id = $_POST['faculty_sub_id'] ?? null;
        if (!$faculty_sub_id) {
            throw new Exception('Faculty subject ID is missing.');
        }

        if ($_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $_FILES['excelFile']['error']);
        }

        $fileType = strtolower(pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION));
        if (!in_array($fileType, ['xlsx', 'xls'])) {
            throw new Exception('Only .xlsx or .xls files are allowed.');
        }

        $filePath = $_FILES['excelFile']['tmp_name'];

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        array_shift($rows);

        $student = new Students();
        $grades = new Grades();
        $successCount = 0;
        $errorMessages = [];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;
            try {
                if (empty(array_filter($row))) {
                    continue;
                }

                if (empty($row[0])) {
                    throw new Exception("Line $lineNumber: Student ID is required");
                }
                if (empty($row[1])) {
                    throw new Exception("Line $lineNumber: First name is required");
                }
                if (empty($row[3])) {
                    throw new Exception("Line $lineNumber: Last name is required");
                }
                if (empty($row[5])) {
                    throw new Exception("Line $lineNumber: Year and section is required");
                }

                $student_id = trim($row[0]);
                $firstname = trim($row[1]);
                $middlename = trim($row[2] ?? '');
                $lastname = trim($row[3]);
                $suffix = trim($row[4] ?? '');
                $year_section = trim($row[5]);
                $email = trim($row[6] ?? '');

                if (!empty($email) && !preg_match('/^[a-zA-Z0-9._%+-]+@wmsu\.edu\.ph$/', $email)) {
                    throw new Exception("Line $lineNumber: Invalid email format. Only @wmsu.edu.ph emails are allowed");
                }

                $existingStudent = $student->getStudentById($student_id);
                if ($existingStudent) {
                    throw new Exception($existingStudent['student_id'] .' already exists.');
                } else {
                    $student->faculty_sub_id = htmlentities($faculty_sub_id);
                    $student->student_id = htmlentities($student_id);
                    $student->student_firstname = ucwords(strtolower(htmlentities($firstname)));
                    $student->student_middlename = !empty($middlename) ? ucwords(strtolower(htmlentities($middlename))) : '';
                    $student->student_lastname = ucwords(strtolower(htmlentities($lastname)));
                    $student->email = htmlentities($email);
                    $student->year_section = htmlentities($year_section);
                    $student->suffix = htmlentities($suffix);

                    if ($student->add()) {
                        $newStudent = $student->getStudentById($student_id);
                        if ($newStudent) {
                            $student_data_id = $newStudent['student_data_id'];

                            $grades->student_data_id = $student_data_id;
                            $grades->faculty_sub_id = htmlentities($faculty_sub_id);

                            if (!$grades->add()) {
                                throw new Exception("Line $lineNumber: Failed to add student " . $newStudent['student_data_id'] . " to subject");
                            }
                        } else {
                            throw new Exception('Failed to retrieve the new student data.');
                        }
                    } else {
                        throw new Exception('Failed to add the new student.');
                    }
                }

                $successCount++;
            } catch (Exception $e) {
                $errorMessages[] = $e->getMessage();
            }
        }

        if ($successCount > 0) {
            $response['success'] = true;
            $response['message'] = "Successfully imported $successCount students.";
            if (!empty($errorMessages)) {
                $response['message'] .= " Some errors occurred: " . implode('; ', $errorMessages);
            }
        } else {
            $response['message'] = "No students imported. Errors: " . implode('; ', $errorMessages);
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    exit();
}
?>