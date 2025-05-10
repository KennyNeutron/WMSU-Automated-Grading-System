<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

require_once '../tools/functions.php';
require_once '../classes/profiling.class.php';
require_once '../classes/faculty_sched.class.php';
require_once '../classes/semester.class.php';
require_once '../classes/faculty_subs.class.php';

$semester = new Semester();
$profiling = new Profiling();
$sched = new Faculty_Sched();
$fac_subs = new Faculty_Subjects();

if (isset($_GET['sched_id'])) {
    $sched_id = $_GET['sched_id'];
    $existingSched = $sched->fetch($sched_id);

    if (!$existingSched) {
        $error_message = "Schedule not found.";
    }
} else {
    $error_message = "Invalid schedule ID.";
}

$semesterarray = $semester->show();
$profile = $profiling->fetchForSched($existingSched['profiling_id']);
$subs = $fac_subs->showByFaculty($_GET['sched_id']);

$current_year = date('Y');

list($start_year, $end_year) = explode('-', $existingSched['school_yr']);
$start_year = trim($start_year);
$end_year = trim($end_year);

$is_current_year = $current_year <= $end_year;
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Subject Assigned';
$faculty_page = 'active';
include '../includes/admin_head.php';
?>

<body>
    <div class="home">
        <div class="side">
            <?php
            require_once('../includes/admin_sidepanel.php')
                ?>
        </div>
        <main>
            <div class="header">
                <?php
                require_once('../includes/admin_header.php')
                    ?>
            </div>

            <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
                <div class="d-flex align-items-center">
                    <a href="./faculty?department_id=<?= $_GET['department_id'] ?>"
                        class="bg-none d-flex align-items-center"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></a>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Subject Assigned</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <div class="details d-flex justify-content-between me-5">
                    <div class="d-flex flex-column">
                        <p class="fw-bolder">Name:
                            <span class="fw-bold brand-color"><?= ucwords($profile['fullName']) ?></span>
                        </p>
                        <p class="fw-bolder">Department: <span
                                class="fw-bold brand-color"><?= ucwords($profile['department_name']) ?></span></p>
                    </div>
                    <div class="d-flex flex-column">
                        <p class="fw-bolder">Designation:
                            <span class="fw-bold brand-color"><?= ucwords($profile['designation']) ?>
                        </p>
                        <p class="fw-bolder">Academic Rank: <span
                                class="fw-bold brand-color"><?= ucwords($profile['acad_type']) ?></span></p>
                    </div>
                </div>

                <div class="content container-fluid mw-100 border rounded shadow p-3">
                    <div class="btn-toolbar d-flex justify-content-between">
                        <div class="btn-group gap-3">
                        </div>

                        <?php if ($is_current_year): ?>
                            <a href="./add_sub_sched?sched_id=<?= $_GET['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>"
                                class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i
                                    class='bx bx-plus-circle'></i> Add Subject</a>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="d-flex flex-column align-items-center">
                        <h3>S.Y. <?= $existingSched['school_yr'] ?></h3>
                        <h4><?= $existingSched['semester_name'] ?></h4>
                    </div>

                    <table id="home_table" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle">#</th>
                                <th rowspan="2" class="align-middle">Subject Code</th>
                                <th rowspan="2" class="align-middle">Name</th>
                                <th rowspan="2" class="align-middle">Prerequisite</th>
                                <th rowspan="2" class="align-middle">Year/Section</th>
                                <th rowspan="2" class="align-middle"># of Students</th>
                                <th colspan="2" class="text-center">Room</th>
                                <th colspan="2" class="text-center">Schedules</th>
                                <th colspan="3" class="text-center">Units</th>
                                <?php if ($is_current_year): ?>
                                    <th rowspan="2" class="text-center" width="5%">Action</th>
                                <?php endif; ?>
                            </tr>
                            <tr>
                                <th>Lecture</th>
                                <th>Laboratory</th>
                                <th>Lecture</th>
                                <th>Laboratory</th>
                                <th>Lec</th>
                                <th>Lab</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            function formatValue($value)
                            {
                                return !empty($value) ? $value : "<span style='color: gray;'>N/A</span>";
                            }

                            foreach ($subs as $sub):
                                $sub_pre = formatValue($sub['sub_prerequisite']);
                                $lec_room = formatValue($sub['lec_room']);
                                $lab_room = formatValue($sub['lab_room']);
                                $lec_days = $sub['lec_days'];
                                $lec_time = formatValue($sub['lec_time']);
                                $lab_days = $sub['lab_days'];
                                $lab_time = formatValue($sub['lab_time']);
                                $lec_units = isset($sub['lec_units']) ? $sub['lec_units'] : 0;
                                $lab_units = isset($sub['lab_units']) ? $sub['lab_units'] : 0;
                                $total_units = $sub['lec_units'] + $sub['lab_units'];
                                ?>
                                <tr>
                                    <td><?= $counter ?></td>
                                    <td><a
                                            href="./sub_students?sched_id=<?= $_GET['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>&faculty_sub_id=<?= $sub['faculty_sub_id'] ?>"><?= $sub['sub_code'] ?></a>
                                    </td>
                                    <td><?= $sub['sub_name'] ?></td>
                                    <td><?= $sub_pre ?></td>
                                    <td><?= $sub['yr_sec'] ?></td>
                                    <td><?= $sub['no_students'] ?></td>
                                    <td><?= $lec_room ?></td>
                                    <td><?= $lab_room ?></td>
                                    <td><?= $lec_days . " (" . $lec_time . ")" ?></td>
                                    <td><?= $lab_days . " (" . $lab_time . ")" ?></td>
                                    <td><?= $lec_units ?></td>
                                    <td><?= $lab_units ?></td>
                                    <td><?= $total_units ?></td>
                                    <?php if ($is_current_year): ?>
                                        <td>
                                            <a
                                                href="./edit_schedule?sched_id=<?= $_GET['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>">
                                                <i class='bx bx-edit text-success fs-4'></i>
                                            </a>
                                            <button class="delete-btn bg-none" data-subject-id="<?= $sub['faculty_sub_id'] ?>">
                                                <i class='bx bx-trash-alt text-danger fs-4'></i>
                                            </button>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <?php
                                $counter++;
                            endforeach;
                            ?>
                        </tbody>


                    </table>
                </div>
            </div>
            <div class="modal fade" id="deleteConfirmationModal" tabindex="-1"
                aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
                <div id="alertContainer"></div>
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this subject?</p>
                            <p class="text-danger"><strong>Warning:</strong> This will also delete all exisiting
                                data/grades associated with this subject.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="./js/main.js"></script>
    <script src="./js/index_table.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            let currentSubId = null;

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    currentSubId = this.getAttribute('data-subject-id');
                    deleteModal.show();
                });
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                if (currentSubId) {
                    fetch('./delete_faculty_sub.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ faculty_sub_id: currentSubId }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlert(data.message, 'success');
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        });
                }
            });

            function showAlert(message, type) {
                const alertContainer = document.getElementById('alertContainer');
                const alertHTML = `
          <div class="alert alert-${type} d-flex flex-row align-items-center gap-2 position-fixed top-0 start-50 translate-middle-x w-auto mt-4 z-index-1050" role="alert">
            <strong>${type === 'success' ? `Successfully Deleted! <i class='bx bx-check-circle' ></i>` : 'Error!'}</strong> ${message}
          </div>
        `;
                alertContainer.innerHTML = alertHTML;
            }
        });
    </script>
</body>

</html>