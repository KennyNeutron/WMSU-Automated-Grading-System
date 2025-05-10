<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

require_once '../classes/course_select.class.php';
require_once '../classes/curr_year.class.php';
require_once '../classes/faculty_subs.class.php';
require_once '../classes/posted_grades.class.php';
require_once '../classes/curri_page.class.php';

$course_curr = new Course_curr();
$curr_year = new Curr_year();
$faculty_subs = new Faculty_Subjects();
$posted_grades = new PostedGrades();
$curr_table = new Curr_table();

$course_id = $_GET['course_id'] ?? '';
$curr_year_id = $_GET['curr_year_id'] ?? '';

$courseName = $course_curr->getCourseNameById($course_id);
$yearRange = $curr_year->getYearRangeById($curr_year_id);

$current_year = date('Y');
$is_previous_year = $yearRange['year_end'] < $current_year;

if ($yearRange) {
    $head = "CURRICULUM {$yearRange['year_start']}-{$yearRange['year_end']}";
} else {
    echo "Invalid Curriculum Year";
}

$postedGradesData = $posted_grades->showByCourse($course_id, $curr_year_id);
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Grades';
$grades_page = 'active';
include '../includes/admin_head.php';
?>

<body>
    <div class="home">
        <div class="side">
            <?php require_once('../includes/admin_sidepanel.php') ?>
        </div>
        <main>
            <div class="header">
                <?php require_once('../includes/admin_header.php') ?>
            </div>

            <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
                <div class="d-flex align-items-center">
                    <a href="./select_department_grade?curr_year_id=<?= $curr_year_id ?>"
                        class="bg-none d-flex align-items-center"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></a>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Grades Posted</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <div class="content mw-100 rounded shadow py-3">
                    <div class="text-center mb-3">
                        <h4><?= $head ?></h4>
                        <h2><?= $courseName['name']; ?></h2>
                    </div>
                    <div class="tab-content py-4 px-3" id="nav-tabContent">

                        <div class="tab-pane fade show active" id="nav-regular" role="tabpanel"
                            aria-labelledby="nav-regular-tab">
                            <div class="search-keyword col-12 flex-lg-grow-0 d-flex my-2 px-2">

                                <div class="input-group">
                                    <input type="text" name="keyword" id="keyword" placeholder="Search"
                                        class="form-control">
                                    <button class="btn btn-outline-secondary brand-bg-color" type="button"><i
                                            class='bx bx-search' aria-hidden="true"></i></button>
                                </div>
                            </div>

                            <table id="subject_table" class="table table-striped table-sm" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th class="text-center" width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($postedGradesData as $index => $grade): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($grade['emp_id']) ?></td>
                                            <td><?= htmlspecialchars($grade['f_name'] . ' ' . $grade['l_name']) ?></td>
                                            <td><?= htmlspecialchars($grade['email']) ?></td>
                                            <td><?= htmlspecialchars($grade['sub_code'] . ' - ' . $grade['sub_name']) ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="./view_grades.php?faculty_sub_id=<?= $grade['faculty_sub_id'] ?>&emp_id=<?= $grade['emp_id'] ?>&curr_year_id=<?= $curr_year_id?>'&course_id=<?= $course_id?>"
                                                    class="btn btn-sm brand-bg-color">
                                                    <i class='bx bx-show'></i> View Grades
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        $(document).ready(function () {
            var dataTableVisiting = $("#subject_table").DataTable({
                dom: "Brtp",
                pageLength: 10,
                buttons: [
                    {
                        remove: "true",
                    },
                ],
                columnDefs: [
                    {
                        targets: [1, 3, 4],
                        orderable: false,
                    },
                ],
            });

            function createFilter(table, inputSelector, columns) {
                var input = $(inputSelector).on("keyup", function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, searchData) {
                    var val = $(inputSelector).val().toLowerCase();
                    if (!val) return true;

                    for (var i = 0; i < columns.length; i++) {
                        if (searchData[columns[i]].toLowerCase().includes(val)) {
                            return true;
                        }
                    }
                    return false;
                });

                return input;
            }

            createFilter(dataTableVisiting, "#keyword", [1, 2, 3, 4]);
        });
    </script>
</body>

</html>