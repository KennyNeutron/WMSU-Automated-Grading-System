<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header('location: ./login.php');
    exit();
}

require_once './classes/faculty_subs.class.php';
require_once './classes/period.class.php';
require_once './classes/component.class.php';
require_once './classes/grades.class.php';
require_once './classes/point_equivalent.class.php';

$selected_faculty_sub_id = $_GET['faculty_sub_id'] ?? null;
$emp_id = $_SESSION['emp_id'];

$fac_subs = new Faculty_Subjects();
$period = new Periods();
$components = new SubjectComponents();
$studentsBySub = new Grades();
$pointEquivalent = new PointEqv();

$all_subs = $fac_subs->getByUser($emp_id);
$subject = $fac_subs->getProf($selected_faculty_sub_id);
$studentList = $studentsBySub->showBySubject2($selected_faculty_sub_id);
$equivalentData = $pointEquivalent->getByFacultySubject($selected_faculty_sub_id);
$sub_type = "";
$percent_type = isset($studentList[0]['percent_type']) ? (float)$studentList[0]['percent_type'] : 0.50;
if (!$studentList || count($studentList) === 0) {
    echo "No student grades available.";
    exit;
}

// Group the students by student_id
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

    $grouped[$student_id]['grades'][$item['subject_type']] = [
        'midterm' => $item['midterm_grade'],
        'final' => $item['final_grade']
    ];
}

// Set lecture/lab percentages
// $selected_percent = $_POST['lecture_percent'] ?? 50;
// $selected_percent = (int)$selected_percent;
// $lab_percent = 100 - $selected_percent;
// $lecture_weight = $selected_percent / 100;
// $lab_weight = $lab_percent / 100;

$percent_type = isset($studentList[0]['percent_type']) ? (float)$studentList[0]['percent_type'] : 0.50;

$selected_percent = (int)($percent_type * 100); // for the dropdown
$lab_percent = 100 - $selected_percent;

$lecture_weight = $percent_type;
$lab_weight = 1 - $percent_type;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lecture_percent'])) {
    $new_percent = (int)$_POST['lecture_percent'];
    if (in_array($new_percent, [40, 50, 60])) {
        $percent_type = $new_percent / 100;
        $fac_subs->updateFacultySubjectPercentType($selected_faculty_sub_id, $percent_type);

        // Reload the page to reflect the updated value
        header("Location: ".$_SERVER['REQUEST_URI']);
        exit();
    }
}

function getPointEquivalent($averageGrade, $gradeEquivalents) {
    $numericalRating = 5.00;
    if ($averageGrade >= $gradeEquivalents['1_00']) $numericalRating = 1.00;
    elseif ($averageGrade >= $gradeEquivalents['1_25']) $numericalRating = 1.25;
    elseif ($averageGrade >= $gradeEquivalents['1_50']) $numericalRating = 1.50;
    elseif ($averageGrade >= $gradeEquivalents['1_75']) $numericalRating = 1.75;
    elseif ($averageGrade >= $gradeEquivalents['2_00']) $numericalRating = 2.00;
    elseif ($averageGrade >= $gradeEquivalents['2_25']) $numericalRating = 2.25;
    elseif ($averageGrade >= $gradeEquivalents['2_50']) $numericalRating = 2.50;
    elseif ($averageGrade >= $gradeEquivalents['2_75']) $numericalRating = 2.75;
    elseif ($averageGrade >= $gradeEquivalents['3_00']) $numericalRating = 3.00;
    return number_format($numericalRating, 2);
}

// Calculate grade values
$studentsWithPoints = [];

foreach ($grouped as $student) {
    $lecture = $student['grades']['lecture'] ?? ['midterm' => 0, 'final' => 0];
    $lab = $student['grades']['laboratory'] ?? ['midterm' => 0, 'final' => 0];

    $lecture_midterm = (float)($lecture['midterm'] ?? 0);
    $lecture_final = (float)($lecture['final'] ?? 0);
    $lab_midterm = (float)($lab['midterm'] ?? 0);
    $lab_final = (float)($lab['final'] ?? 0);

    $lecture_overall = ($lecture_midterm + $lecture_final) / 2;
    $lab_overall = ($lab_midterm + $lab_final) / 2;

    $lecture_with_percent = $lecture_overall * $lecture_weight;
    $lab_with_percent = $lab_overall * $lab_weight;
    $percentage_average = $lecture_with_percent + $lab_with_percent;

    $point_equiv = $percentage_average;
    $mapped_point = getPointEquivalent($point_equiv, $equivalentData);

    $studentsWithPoints[] = [
        'student_id' => $student['student_id'],
        'fullName' => $student['fullName'],
        'email' => $student['email'],
        'year_section' => $student['year_section'],
        'lecture' => $lecture_overall,
        'lab' => $lab_overall,
        'percentage_average' => $percentage_average,
        'point_equiv' => $point_equiv,
        'numerical_rating' => $mapped_point
    ];
}

// Sort students by point_equiv for ranking
usort($studentsWithPoints, fn($a, $b) => $b['point_equiv'] <=> $a['point_equiv']);
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = $subject['sub_code'] . " (" . $subject['yr_sec'] . ') - Complete Grades';
include './includes/head.php';
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = $subject['sub_code'] . " (" . $subject['yr_sec'] . ') - Complete Grades';
include './includes/head.php';
?>

<body>
<div class="home">
    <div class="side"><?php require_once('./includes/sidepanel.php'); ?></div>
    <main>
        <div class="header"><?php require_once('./includes/header.php'); ?></div>

        <div class="p-3 title_page shadow" style="background-color: whitesmoke;">
            <div class="d-flex align-items-center">
                <a href="./select_subject_students" class="bg-none">
                    <i class='bx bx-chevron-left fs-2 brand-color'></i>
                </a>
                <div class="container-fluid d-flex justify-content-center">
                    <span class="fs-2 fw-bold brand-color">Complete Grades</span>
                </div>
            </div>
        </div>

        <div class="m-4">
            <?php if ($subject): ?>
                <div class="text-center mb-4">
                    <h3 class="brand-color"><?= ucwords($subject['sub_name']) ?></h3>
                    <h4><?= $subject['sub_code'] ?></h4>
                    <h4>(<?= $subject['yr_sec'] ?>)</h4>
                </div>


                <div class="mb-3 d-flex justify-content-between">
                    <div id="MyButtons" class="mb-2"></div>
                    <div class="input-group" style="width: 40%;">
                        <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control">
                        <button class="btn btn-outline-secondary brand-bg-color" type="button"><i class='bx bx-search'></i></button>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                <div class="d-flex justify-content-end">
                    <form method="post" class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <label for="lecture_percent" class="fw-bold mb-0">Lecture:</label>
                            <select name="lecture_percent" id="lecture_percent" class="form-select form-select-sm" style="width: 80px;" onchange="updateLabPercent();" >
    <option value="40" <?= $selected_percent == 40 ? 'selected' : '' ?>>40%</option>
    <option value="50" <?= $selected_percent == 50 ? 'selected' : '' ?>>50%</option>
    <option value="60" <?= $selected_percent == 60 ? 'selected' : '' ?>>60%</option>
</select>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <label class="fw-bold mb-0">Lab:</label>
                            <input type="text" id="lab_percent" readonly class="form-control-plaintext fw-bold" style="width: 50px;" value="<?= (100 - $selected_percent) ?>%">
                        </div>

                        <button type="submit" class="btn btn-sm btn-outline-secondary brand-bg-color">Calculate</button>
                    </form>
                </div>
</div>

                <table id="students" class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Year & Section</th>
                        <th>Lecture Grade</th>
                        <th>Lab Grade</th>
                        <th>Average Grade</th>
                        <th>Point Equivalent</th>
                        <th class="text-center">Rank</th>
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
                            <td><?= $s['percentage_average'] > 0 ? number_format($s['percentage_average'], 2)  : 'No Grade' ?></td>
                            <td><?= $s['numerical_rating'] != 'N/A' ? $s['numerical_rating'] : 'No Grade' ?></td>
                            <?php
                            $bgColor = match ($rank) {
                                1 => '#55dd75',
                                2 => '#469fff',
                                3 => '#edc54d',
                                default => 'transparent'
                            };
                            ?>
                            <td style="background-color: <?= $bgColor ?>; text-align: center;"><?= $rank++ ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>
    <?php require_once('./includes/js.php'); ?>
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

       
        function updateLabPercent() {
        const lecturePercent = parseInt($('#lecture_percent').val());
        const labPercent = 100 - lecturePercent;
        $('#lab_percent').val(labPercent + '%');
    }
    </script>
</body>

</html>