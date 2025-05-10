<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

require_once '../classes/posted_grades.class.php';
require_once '../classes/faculty_subs.class.php';

$faculty_sub_id = $_GET['faculty_sub_id'] ?? '';
$emp_id = $_GET['emp_id'] ?? '';

$posted_grades = new PostedGrades();
$faculty_subs = new Faculty_Subjects();
$subject = $faculty_subs->getProf($faculty_sub_id);
$grades = $posted_grades->getByFacSub($emp_id, $faculty_sub_id);
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
                    <a href="./grade_subject_table?curr_year_id=<?= $_GET['curr_year_id'] . '&course_id=' . $_GET['course_id'] ?>"
                        class="bg-none d-flex align-items-center"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></a>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Grades Posted</span>
                    </div>
                </div>
            </div>
            <div class="tab-content py-4 px-5" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-regular" role="tabpanel"
                    aria-labelledby="nav-regular-tab">
                    <div class="d-flex flex-column align-items-center mb-5">
                        <h3 class="brand-color"><?= $subject ? ucwords($subject['sub_name']) : '' ?></h3>
                        <h4><?= $subject['sub_code'] ?></h4>
                    </div>
                    <div class="search-keyword col-12 flex-lg-grow-0 d-flex justify-content-between gap-3 my-4 px-2">
                        <div class="d-flex justify-content-between">
                        </div>
                        <div class="input-group" style="width: 40% !important;">
                            <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control">
                            <button class="btn btn-outline-secondary brand-bg-color" type="button"><i
                                    class='bx bx-search' aria-hidden="true"></i></button>
                        </div>
                    </div>

                    <table id="grades_table" class="table table-striped table-sm" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            foreach ($grades as $grade): ?>
                                <tr>
                                    <td><?= $count ?></td>
                                    <td><?= $grade['student_id'] ?></td>
                                    <td><?= $grade['student_last'] . ', ' . $grade['student_first'] ?></td>
                                    <td
                                        class="<?= ($grade['point_eqv'] === 'INC' || $grade['point_eqv'] == 5.00) ? 'text-danger' : '' ?>">
                                        <?= is_numeric($grade['point_eqv']) ? number_format($grade['point_eqv'], 2) : htmlspecialchars($grade['point_eqv']) ?>
                                    </td>
                                </tr>
                                <?php
                                $count++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        $(document).ready(function () {
            var dataTableVisiting = $("#grades_table").DataTable({
                dom: "Brtp",
                pageLength: 10,
                buttons: [
                    {
                        remove: "true",
                    },
                ],
                columnDefs: [
                    {
                        targets: [1],
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

            createFilter(dataTableVisiting, "#keyword", [1, 2]);
        });
    </script>
</body>

</html>