<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header('location: ./login.php');
    exit();
}

require_once './classes/grades.class.php';
require_once './classes/period.class.php';
require_once './classes/component_scores.class.php';
require_once './classes/point_equivalent.class.php';

$grades = new Grades();
$period = new Periods();
$scores = new ComponentScores();
$pointEqv = new PointEqv();

$faculty_sub_id = $_POST['faculty_sub_id'] ?? null;
$active_period = $_POST['active_period'] ?? 'midterm';

if (!$faculty_sub_id) {
    $_SESSION['error'] = "Invalid subject selection";
    header("location: subject_students.php?faculty_sub_id=$faculty_sub_id");
    exit();
}

// Get all students in this subject
$studentList = $grades->showBySubject($faculty_sub_id);

// Get grading components for the active period
$gradingComponents = ($active_period === 'finalterm') ? 
    $period->showFinalterm($faculty_sub_id) : 
    $period->showMidterm($faculty_sub_id);

// Get midterm components (needed for final term calculation)
$midtermComponents = $period->showMidterm($faculty_sub_id);

// Get grade equivalents
$gradeEquivalents = $pointEqv->getByFacultySubject($faculty_sub_id);

foreach ($studentList as $student) {
    $grades_id = $student['grades_id'];
    $avgGrade = 0;
    $midtermAvg = 0;
    
    // Calculate average for current period
    foreach ($gradingComponents as $component) {
        $avgGrade += $scores->calculateWeightByComponent($grades_id, $component['component_id']) ?: 0;
    }
    $avgGrade = round($avgGrade, 2);
    
    // For final term, calculate midterm average
    if ($active_period === 'finalterm') {
        foreach ($midtermComponents as $component) {
            $midtermAvg += $scores->calculateWeightByComponent($grades_id, $component['component_id']) ?: 0;
        }
        $midtermAvg = round($midtermAvg, 2);
    }
    
    // Update grades based on period
    if ($active_period === 'midterm') {
        $grades->updateMidtermGrade($grades_id, $avgGrade);
    } else {
        $grades->updateFinalGrade($grades_id, $avgGrade, $midtermAvg);
    }
}

$_SESSION['success'] = "Grades for all students have been successfully applied!";
header("location: subject_students.php?faculty_sub_id=$faculty_sub_id&active_period=$active_period");
exit();
?>