<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
    header('location: ./login.php');
    exit();
}

require_once './classes/faculty_subs.class.php';
require_once './classes/period.class.php';
require_once './classes/component.class.php';
require_once './classes/grades.class.php';
require_once './classes/point_equivalent.class.php';

$selected_faculty_sub_id = isset($_GET['faculty_sub_id']) ? $_GET['faculty_sub_id'] : null;
$emp_id = $_SESSION['emp_id'];

$fac_subs = new Faculty_Subjects();
$period = new Periods();
$components = new SubjectComponents();
$studentsBySub = new Grades();
$pointEquivalent  = new PointEqv();

$all_subs = $fac_subs->getByUser($_SESSION['emp_id']);
$subject = $fac_subs->getProf($selected_faculty_sub_id);
$studentList = $studentsBySub->showBySubject2($selected_faculty_sub_id);
$equivalentData  = $pointEquivalent ->getByFacultySubject($selected_faculty_sub_id);
$sub_type = "";


if (!$studentList || count($studentList) === 0) {
    echo "No student grades available.";
    exit;
}
// Group the students by student_id and subject_type
$grouped = [];

foreach ($studentList as $item) {
    $student_id = $item['student_id'];

    if (!isset($grouped[$student_id])) {
        $grouped[$student_id] = [
            'fullName' => $item['fullName'],
            'email' => $item['email'],
            'year_section' => $item['year_section'],
            'student_id' => $student_id,
            'grades' => []
        ];
    }

    // Store lecture or lab grades
    $grouped[$student_id]['grades'][$item['subject_type']] = [
        'midterm' => $item['midterm_grade'],
        'final' => $item['final_grade']
    ];
}

// Now compute overall, point equivalent, and rank
$studentsWithPoints = [];

foreach ($grouped as $student) {
    $lecture = $student['grades']['lecture'] ?? ['midterm' => 0, 'final' => 0];
    $lab = $student['grades']['laboratory'] ?? ['midterm' => 0, 'final' => 0];

    // Safely cast to float to prevent type errors
    $lecture_midterm = is_numeric($lecture['midterm']) ? (float)$lecture['midterm'] : 0;
    $lecture_final = is_numeric($lecture['final']) ? (float)$lecture['final'] : 0;

    $lab_midterm = is_numeric($lab['midterm']) ? (float)$lab['midterm'] : 0;
    $lab_final = is_numeric($lab['final']) ? (float)$lab['final'] : 0;

    $lecture_overall = $lecture_midterm ;
    $lab_overall = $lab_midterm;

    $point_equiv = ($lecture_overall + $lab_overall) / 2;

    // Map point equivalent to the student's calculated grade
    $mapped_point = getPointEquivalent($point_equiv, $equivalentData);

    $studentsWithPoints[] = [
        'student_id' => $student['student_id'],
        'fullName' => $student['fullName'],
        'email' => $student['email'],
        'year_section' => $student['year_section'],
        'lecture' => $lecture_overall,
        'lab' => $lab_overall,
        'point_equiv' => $point_equiv,
        'numerical_rating' => $mapped_point
    ];
}

// Sort by point_equiv descending for ranking
usort($studentsWithPoints, fn($a, $b) => $b['point_equiv'] <=> $a['point_equiv']);function getPointEquivalent($averageGrade, $gradeEquivalents) {
    $numericalRating = 'N/A';

    if ($averageGrade >= $gradeEquivalents['1_00']) {
        $numericalRating = 1.00;
    } elseif ($averageGrade >= $gradeEquivalents['1_25']) {
        $numericalRating = 1.25;
    } elseif ($averageGrade >= $gradeEquivalents['1_50']) {
        $numericalRating = 1.50;
    } elseif ($averageGrade >= $gradeEquivalents['1_75']) {
        $numericalRating = 1.75;
    } elseif ($averageGrade >= $gradeEquivalents['2_00']) {
        $numericalRating = 2.00;
    } elseif ($averageGrade >= $gradeEquivalents['2_25']) {
        $numericalRating = 2.25;
    } elseif ($averageGrade >= $gradeEquivalents['2_50']) {
        $numericalRating = 2.50;
    } elseif ($averageGrade >= $gradeEquivalents['2_75']) {
        $numericalRating = 2.75;
    } elseif ($averageGrade >= $gradeEquivalents['3_00']) {
        $numericalRating = 3.00;
    } else {
        $numericalRating = 5.00;
    }

    
   
    return number_format($numericalRating, 2, '.', '');
}


?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = $subject['sub_code'] . " (" . $subject['yr_sec'] . ') - Complete Grades';
$student_page = 'active';
include './includes/head.php';
?>

<body>
    <div class="home">
        <div class="side">
            <?php require_once('./includes/sidepanel.php'); ?>
        </div>
        <main>
            <div class="header">
                <?php require_once('./includes/header.php'); ?>
            </div>

            <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
                <div class="d-flex align-items-center">
                    <a href="./select_subject_students" class="bg-none">
                        <i class='bx bx-chevron-left fs-2 brand-color'></i>
                    </a>

                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Midterm Complete Grades</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <?php if (!empty($_GET['faculty_sub_id'])): ?>
                    <div class="d-flex flex-column align-items-center">
                        <h3 class="brand-color"><?= $subject ? ucwords($subject['sub_name']) : '' ?></h3>
                        <h4><?= $subject ? $subject['sub_code'] . $sub_type : "" ?></h4>
                        <h4 style="margin: 0; padding: 0;">(<?= $subject ? $subject['yr_sec'] : "" ?>)</h4>
                    </div>
                    <div class="search-keyword col-12 flex-lg-grow-0 d-flex justify-content-between gap-3 my-4 px-2">
                        <div class="d-flex justify-content-between">
                            <div id="MyButtons" class="d-flex me-1 mb-md-2 mb-lg-0 col-12 col-md-auto"></div>
                          
                        </div>
                        <div class="input-group" style="width: 40% !important;">
                            <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control">
                            <button class="btn btn-outline-secondary brand-bg-color" type="button"><i class='bx bx-search'
                                    aria-hidden="true"></i></button>
                        </div>
                    </div>

                    <table id="students" class="table table-striped table-sm" style="width:100%">
            <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Student ID</th>
            <th scope="col">Full Name (Last, First M.I.)</th>
            <th scope="col">Email</th>
            <th scope="col">Year & Section</th>
            <th scope="col">Lecture Grade</th>
            <th scope="col">Lab Grade</th>
            <th scope="col">Average Grade</th>
            <th class="text-center" scope="col">Rank</th>
        </tr>
    </thead>
    <tbody>
        <?php $rank = 1; ?>
        <?php foreach ($studentsWithPoints as $index => $s): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($s['student_id']) ?></td>
            <td><?= htmlspecialchars($s['fullName']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= htmlspecialchars($s['year_section']) ?></td>
            <td><?= $s['lecture'] > 0 ? number_format($s['lecture'], 2)  : 'No Grade' ?></td>
            <td><?= $s['lab'] > 0 ? number_format($s['lab'], 2)  : 'No Grade' ?></td>
            <td><?= $s['point_equiv'] > 0 ? number_format($s['point_equiv'], 2)  : 'No Grade' ?></td>

            <?php
    $bgColor = match ($rank) {
        1 => '#55dd75', // Green
        2 => '#469fff', // Blue
        3 => '#edc54d', // Yellow
        default => 'transparent'
    };
?>
<td style="background-color: <?= $bgColor ?>; text-align: center;">
    <?= $rank++ ?>
</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

                <?php endif; ?>
            </div>
   

        </main>
    </div>



    <script>
        $(document).ready(function () {
            dataTable = $("#students").DataTable({
                dom: 'Brtp',
                responsive: true,
                fixedHeader: true,
                pageLength: 15,
                buttons: [{
                    extend: 'pdf',
                    split: ['excel', 'csv'],
                }],
                'columnDefs': [{
                    'targets': [3],
                    'orderable': false,
                }]
            }); // This was the line with the issue - removed extra parenthesis

            dataTable.buttons().container().appendTo($('#MyButtons'));

            var table = dataTable;
            var filter = createFilter(table, [1, 2]);

            function createFilter(table, columns) {
                var input = $('input#keyword').on("keyup", function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (
                    settings,
                    searchData,
                    index,
                    rowData,
                    counter
                ) {
                    var val = input.val().toLowerCase();

                    for (var i = 0, ien = columns.length; i < ien; i++) {
                        if (searchData[columns[i]].toLowerCase().indexOf(val) !== -1) {
                            return true;
                        }
                    }

                    return false;
                });

                return input;
            }
        });
    </script>
  
</body>

</html>