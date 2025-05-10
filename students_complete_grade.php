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

$labData = null;
$studentListLab = [];
$hasLabData = false;

if ($subject['subject_type'] == 'lecture' || $subject['subject_type'] == 'combined' ) {
    $labData = $fac_subs->getLabData($subject['curr_id']);
    if ($labData !== false && isset($labData['faculty_sub_id'])) {
        $studentListLab = $studentsBySub->showBySubject($labData['faculty_sub_id']);
        $hasLabData = true;
    }
} 
// elseif ($subject['subject_type'] == 'laboratory') {
//     $studentListLab = $studentList; // because you are already in lab
//     $hasLabData = true;
// }

$studentsWithGrades = [];
foreach ($studentList as $item) {
    $labGrade = null;
    foreach ($studentListLab as $labItem) {
        if ($labItem['student_id'] == $item['student_id']) {
            $labGrade = $labItem;
            break;
        }
    }

    $lectureMidtermIsINC = isset($item['midterm_grade']) && strtoupper($item['midterm_grade']) == 'INC';
    $lectureFinalIsINC = isset($item['final_grade']) && strtoupper($item['final_grade']) == 'INC';
    $lectureHasINC = $lectureMidtermIsINC || $lectureFinalIsINC;

    $lectureMidterm = (!$lectureMidtermIsINC && isset($item['midterm_grade']) && $item['midterm_grade'] !== null && $item['midterm_grade'] !== '') ? (float) $item['midterm_grade'] : null;
    $lectureFinal = (!$lectureFinalIsINC && isset($item['final_grade']) && $item['final_grade'] !== null && $item['final_grade'] !== '') ? (float) $item['final_grade'] : null;
    $lectureGrade = null;
    if (!$lectureHasINC && $lectureMidterm !== null && $lectureFinal !== null) {
        $lectureGrade = ($lectureMidterm + $lectureFinal) / 2;
    }

    $labMidtermIsINC = ($labGrade && isset($labGrade['midterm_grade']) && strtoupper($labGrade['midterm_grade']) == 'INC');
    $labFinalIsINC = ($labGrade && isset($labGrade['final_grade']) && strtoupper($labGrade['final_grade']) == 'INC');
    $labHasINC = $labMidtermIsINC || $labFinalIsINC;

    $labMidterm = (!$labMidtermIsINC && $labGrade && isset($labGrade['midterm_grade']) && $labGrade['midterm_grade'] !== null && $labGrade['midterm_grade'] !== '') ? (float) $labGrade['midterm_grade'] : null;
    $labFinal = (!$labFinalIsINC && $labGrade && isset($labGrade['final_grade']) && $labGrade['final_grade'] !== null && $labGrade['final_grade'] !== '') ? (float) $labGrade['final_grade'] : null;
    $labGradeValue = null;
    if (!$labHasINC && $labMidterm !== null && $labFinal !== null) {
        $labGradeValue = ($labMidterm + $labFinal) / 2;
    }

    $averageGrade = null;
    $hasINC = $lectureHasINC || $labHasINC;
    if (!$hasINC) {
        if ($lectureGrade !== null && $labGradeValue !== null) {
            $averageGrade = ($lectureGrade + $labGradeValue) / 2;
        } elseif ($lectureGrade !== null) {
            $averageGrade = $lectureGrade;
        } elseif ($labGradeValue !== null) {
            $averageGrade = $labGradeValue;
        }
    }

    $numericalRating = null;
    if ($averageGrade !== null && $averageGrade > 0 && !empty($gradeEquivalents)) {
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
        'student_data_id' => $item['student_data_id'],
        'student_id' => $item['student_id'],
        'fullName' => $item['fullName'],
        'email' => $item['email'],
        'year_section' => $item['year_section'],
        'lecture_grade' => $lectureHasINC ? 'INC' : $lectureGrade,
        'lab_grade' => $labHasINC ? 'INC' : $labGradeValue,
        'average_grade' => $averageGrade,
        'numerical_rating' => $hasINC ? 'INC' : $numericalRating,
        'has_inc' => $hasINC
    ];
}

usort($studentsWithGrades, function ($a, $b) {
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
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Complete Grades</span>
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
                            <button type="button" class="btn brand-bg-color" id="postBtn"><i class='bx bx-upload'></i> Post
                                Grades</button>
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
                                <th scope="col">Point Equivalent</th>
                                <th class="text-center" scope="col">Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($studentsWithGrades as $index => $student): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($student['student_id']) ?></td>
                                    <td><?= htmlspecialchars($student['fullName']) ?></td>
                                    <td><?= htmlspecialchars($student['email']) ?></td>
                                    <td><?= htmlspecialchars($student['year_section']) ?></td>
                                    <td
                                        style="color: <?= ($student['lecture_grade'] === null || $student['lecture_grade'] <= 0) ? 'grey' : 'inherit' ?>; background-color:rgb(255, 212, 212);">
                                        <?= ($student['lecture_grade'] === 'INC') ? 'INC' : (($student['lecture_grade'] !== null && $student['lecture_grade'] > 0) ? $student['lecture_grade'] . '%' : 'No Grade') ?>
                                    </td>
                                    <td
                                        style="color: <?= ($student['lab_grade'] === null || $student['lab_grade'] <= 0) ? 'grey' : 'inherit' ?>;">
                                        <?= ($student['lab_grade'] === 'INC') ? 'INC' : (($student['lab_grade'] !== null && $student['lab_grade'] > 0) ? $student['lab_grade'] . '%' : 'No Grade') ?>
                                    </td>
                                    <td
                                        style="color: <?= ($student['numerical_rating'] === null || $student['average_grade'] <= 0) ? 'grey' : 'inherit' ?>;">
                                        <?= ($student['numerical_rating'] === 'INC') ? 'INC' : (($student['numerical_rating'] !== null && $student['average_grade'] > 0) ? number_format($student['numerical_rating'], 2) : 'No Grade') ?>
                                    </td>
                                    <td class="text-center <?=
                                        $student['rank'] === 1 ? 'rank-1' :
                                        ($student['rank'] === 2 ? 'rank-2' :
                                            ($student['rank'] === 3 ? 'rank-3' : ''))
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

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmPostModal" tabindex="-1" aria-labelledby="confirmPostModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmPostModalLabel">Confirm Post Grades</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to post these grades? <span class="text-danger"> This action cannot be
                        undone.</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn brand-bg-color" id="confirmPostBtn">Post Grades</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Grades have been successfully posted!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn brand-bg-color" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
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
            });

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
        })

        $('#postBtn').click(function () {
            $('#confirmPostModal').modal('show');
        });

        $('#confirmPostBtn').click(function () {
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Posting...');

            let studentsToPost = [];
            <?php foreach ($studentsWithGrades as $student): ?>
                <?php if ($student['numerical_rating'] !== null): ?>
                    studentsToPost.push({
                        student_data_id: '<?= $student['student_data_id'] ?>',
                        point_eqv: '<?= $student['numerical_rating'] ?>',
                    });
                <?php endif; ?>
            <?php endforeach; ?>

            $.ajax({
                url: 'post_grades.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    faculty_sub_id: '<?= $selected_faculty_sub_id ?>',
                    emp_id: '<?= $emp_id ?>',
                    students: studentsToPost
                }),
                dataType: 'json',
                success: function (response) {
                    $('#confirmPostModal').modal('hide');
                    $('#confirmPostBtn').prop('disabled', false).html('Post Grades');

                    if (response.success) {
                        $('#successModal').modal('show');
                    } else {
                        let errorMsg = 'Some grades failed to post:\n\n';
                        response.results.forEach(result => {
                            if (!result.success) {
                                errorMsg += `Student ID: ${result.student_data_id}\n`;
                                errorMsg += `Error: ${result.message}\n\n`;
                            }
                        });
                        alert(errorMsg);
                    }
                },
                error: function (xhr, status, error) {
                    $('#confirmPostModal').modal('hide');
                    $('#confirmPostBtn').prop('disabled', false).html('Post Grades');

                    try {
                        const response = JSON.parse(xhr.responseText);
                        alert('Error: ' + (response.error || 'Unknown error occurred'));
                    } catch (e) {
                        alert('Error: ' + error);
                    }
                }
            });
        });
    </script>
</body>

</html>