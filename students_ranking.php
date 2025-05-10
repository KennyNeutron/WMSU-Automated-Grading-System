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

$fac_subs = new Faculty_Subjects();
$period = new Periods();
$components = new SubjectComponents();
$studentsBySub = new Grades();
$pointEqv = new PointEqv();

$all_subs = $fac_subs->getByUser($_SESSION['emp_id']);
$subject = $fac_subs->getProf($selected_faculty_sub_id);
$studentList = $studentsBySub->showBySubject($selected_faculty_sub_id);
$gradeEquivalents = $pointEqv->getByFacultySubject($selected_faculty_sub_id);
$sub_type = "";

if ($subject['subject_type'] == 'lecture') {
    $sub_type = ' - LEC';
} elseif ($subject['subject_type'] == 'laboratory') {
    $sub_type = ' - LAB';
} elseif ($subject['subject_type'] == 'combined') {
    $sub_type = '';
}

$studentsWithGrades = [];
foreach ($studentList as $item) {
    $midtermIsINC = isset($item['midterm_grade']) && strtoupper($item['midterm_grade']) == 'INC';
    $finalIsINC = isset($item['final_grade']) && strtoupper($item['final_grade']) == 'INC';
    $hasINC = $midtermIsINC || $finalIsINC;

    $midtermAvg = (!$midtermIsINC && isset($item['midterm_grade']) && $item['midterm_grade'] !== null && $item['midterm_grade'] !== '') ? (float) $item['midterm_grade'] : null;
    $finalGrade = (!$finalIsINC && isset($item['final_grade']) && $item['final_grade'] !== null && $item['final_grade'] !== '') ? (float) $item['final_grade'] : null;

    $averageGrade = null;
    if (!$hasINC && $midtermAvg !== null && $finalGrade !== null) {
        $averageGrade = ($midtermAvg + $finalGrade) / 2;
    }

    $numericalRating = null;
    if (!$hasINC && $averageGrade !== null && $averageGrade > 0 && !empty($gradeEquivalents)) {
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
    }

    $studentsWithGrades[] = [
        'student_id' => $item['student_id'],
        'fullName' => $item['fullName'],
        'email' => $item['email'],
        'year_section' => $item['year_section'],
        'midterm_grade' => $midtermIsINC ? 'INC' : $midtermAvg,
        'final_grade' => $finalIsINC ? 'INC' : $finalGrade,
        'average_grade' => $averageGrade,
        'numerical_rating' => $hasINC ? 'INC' : $numericalRating,
        'has_inc' => $hasINC,
        'subject_type' => $subject['subject_type'],
    ];
}

usort($studentsWithGrades, function ($a, $b) {
    // Students with INC should come after those with grades
    if ($a['has_inc'] && !$b['has_inc'])
        return 1;
    if (!$a['has_inc'] && $b['has_inc'])
        return -1;
    if ($a['has_inc'] && $b['has_inc'])
        return 0;

    if ($a['average_grade'] === null && $b['average_grade'] === null)
        return 0;
    if ($a['average_grade'] === null)
        return 1;
    if ($b['average_grade'] === null)
        return -1;
    return $b['average_grade'] <=> $a['average_grade'];
});

$rank = 1;
$prevGrade = null;
$prevRank = 1;
foreach ($studentsWithGrades as &$student) {
    if ($student['has_inc']) {
        $student['rank'] = '-';
        continue;
    }

    if ($student['average_grade'] === null || $student['average_grade'] <= 0) {
        $student['rank'] = 'No Grade';
        continue;
    }

    if ($prevGrade !== null && $student['average_grade'] < $prevGrade) {
        $rank = $prevRank + 1;
    }
    $student['rank'] = $rank;
    $prevGrade = $student['average_grade'];
    $prevRank = $rank;
}
unset($student);
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = $subject['sub_code'] . " (" . $subject['yr_sec'] . ') - Students Ranking';
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
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Students Ranking</span>
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
                                <th scope="col">Subject </th>
                                <th scope="col">Student ID</th>
                                <th scope="col">Full Name (Last, First M.I.)</th>
                                <th scope="col">Email</th>
                                <th scope="col">Year & Section</th>
                                <th scope="col">Midterm Grade</th>
                                <th scope="col">Final Grade</th>
                            
                                <th scope="col">Average Grade</th>
                                <th class="text-center" scope="col">Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($studentsWithGrades as $index => $student): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($student['subject_type']) ?></td>
                                    <td><?= htmlspecialchars($student['student_id']) ?></td>
                                    <td><?= htmlspecialchars($student['fullName']) ?></td>
                                    <td><?= htmlspecialchars($student['email']) ?></td>
                                    <td><?= htmlspecialchars($student['year_section']) ?></td>
                                    <td style="color: <?= ($student['midterm_grade'] === null || $student['midterm_grade'] <= 0) ? 'grey' : 'inherit' ?>;">
                                        <?=
                                            ($student['midterm_grade'] === 'INC') ? 'INC' :
                                            (($student['midterm_grade'] !== null && $student['midterm_grade'] > 0) ? $student['midterm_grade']  : 'No Grade')
                                            ?>
                                    </td>
                                    <td style="color: <?= ($student['final_grade'] === null || $student['final_grade'] <= 0) ? 'grey' : 'inherit' ?>;">
                                        <?=
                                            ($student['final_grade'] === 'INC') ? 'INC' :
                                            (($student['final_grade'] !== null && $student['final_grade'] > 0) ? $student['final_grade']  : 'No Grade')
                                            ?>
                                    </td>
                                    <td>
                                    <?= 
                                        ($student['has_inc']) ? 'INC' :
                                        (($student['average_grade'] !== null && $student['average_grade'] > 0) ? number_format($student['average_grade'], 2) : 'No Grade')
                                    ?>
                                </td>
                                    <td class="text-center <?=
                                        ($student['rank'] === 1 && !$student['has_inc']) ? 'rank-1' : (
                                            ($student['rank'] === 2 && !$student['has_inc']) ? 'rank-2' : (
                                                ($student['rank'] === 3 && !$student['has_inc']) ? 'rank-3' : ''
                                            )
                                        )
                                        ?>">
                                        <?= $student['rank'] ?>
                                    </td>
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
    </script>
</body>

</html>