<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

require_once '../tools/functions.php';
require_once '../classes/profiling.class.php';
require_once '../classes/faculty_sched.class.php';
require_once '../classes/faculty_subs.class.php';
require_once '../classes/students.class.php';
require_once '../classes/grades.class.php';

$profiling = new Profiling();
$sched = new Faculty_Sched();
$fac_subs = new Faculty_Subjects();
$students = new Students();
$studentsBySub = new Grades();

$prof = $sched->fetch($_GET['sched_id']);
$subject = $fac_subs->getProf($_GET['faculty_sub_id']);
$studentList = $studentsBySub->showBySubject($_GET['faculty_sub_id']);

$current_year = date('Y');

list($start_year, $end_year) = explode('-', $prof['school_yr']);
$start_year = trim($start_year);
$end_year = trim($end_year);

$is_current_year = $current_year <= $end_year;
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Master List';
$faculty_page = 'active';
include '../includes/admin_head.php';
?>

<body>
    <div class="home">
        <div class="side">
            <?php require_once('../includes/admin_sidepanel.php'); ?>
        </div>
        <main>
            <div class="header">
                <?php require_once('../includes/admin_header.php'); ?>
            </div>

            <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
                <div class="d-flex align-items-center">
                    <a href="./faculty_schedule?sched_id=<?= $_GET['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>"
                        class="bg-none d-flex align-items-center">
                        <i class='bx bx-chevron-left fs-2 brand-color'></i>
                    </a>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Master List</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <div class="details d-flex justify-content-between me-5">
                    <div class="d-flex flex-column">
                        <p class="fw-bolder">Professor: <span
                                class="fw-bold brand-color"><?= ucwords($prof['fullName']) ?></span></p>
                        <p class="fw-bolder">Year & Section: <span
                                class="fw-bold brand-color"><?= $subject['yr_sec'] ?></span></p>
                    </div>
                </div>

                <div class="content container-fluid mw-100 border rounded shadow p-3">

                    <div class="btn-toolbar d-flex justify-content-end gap-1">
                        <?php if ($is_current_year): ?>
                            <button type="button" class="btn btn-outline-secondary brand-bg-color" data-bs-toggle="modal"
                                data-bs-target="#importExcelModal">
                                <i class='bx bx-import'></i> Import Excel
                            </button>

                            <a href="./add_student?sched_id=<?= $_GET['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>&faculty_sub_id=<?= $_GET['faculty_sub_id'] ?>"
                                class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i
                                    class='bx bx-plus-circle'></i> Add Student</a>
                        <?php endif; ?>
                    </div>

                    <!-- Import Excel Modal -->
                    <div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel"
                        aria-hidden="true">
                        <div id="alertContainer2"></div>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="importExcelModalLabel">Import Students from Excel</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="importExcelForm" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="excelFile" class="form-label">Select Excel File</label>
                                            <input class="form-control" type="file" id="excelFile" name="excelFile"
                                                accept=".xlsx, .xls" required>
                                            <small class="text-muted">Only .xlsx or .xls files are accepted</small>
                                        </div>
                                        <div class="mb-3">
                                            <a href="../templates/student_import_template.xlsx" download
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class='bx bx-download'></i> Download Template
                                            </a>
                                        </div>
                                        <input type="hidden" name="faculty_sub_id"
                                            value="<?= $_GET['faculty_sub_id'] ?>">
                                        <input type="hidden" name="sched_id" value="<?= $_GET['sched_id'] ?>">
                                        <input type="hidden" name="department_id" value="<?= $_GET['department_id'] ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Import</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex flex-column align-items-center">
                        <h3 class="brand-color"><?= ucwords($subject['sub_name']) ?></h3>
                        <h4><?= $subject['sub_code'] ?></h4>
                    </div>

                    <table id="student_table" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student ID</th>
                                <th>Student Name (Last, First, MI)</th>
                                <th>Email</th>
                                <th>Year & Section</th>
                                <?php if ($is_current_year): ?>
                                    <th width="5%">Action</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            foreach ($studentList as $student): ?>
                                <tr>
                                    <td><?= $counter ?></td>
                                    <td><?= $student['student_id'] ?></td>
                                    <td><?= $student['fullName'] ?></td>
                                    <td><?= $student['email'] ?></td>
                                    <td><?= $student['year_section'] ?></td>
                                    <?php if ($is_current_year): ?>
                                        <td>
                                            <a
                                                href="./edit_student?sched_id=<?= $_GET['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>&faculty_sub_id=<?= $_GET['faculty_sub_id'] ?>&student_data_id=<?= $student['student_data_id'] ?>">
                                                <i class='bx bx-edit text-success fs-4'></i>
                                            </a>
                                            <button class="delete-btn bg-none" data-subject-id="<?= $student['grades_id'] ?>">
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
                <div id="alertContainer1"></div>
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this student in the subject?
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
    <script>
        $(document).ready(function () {
            dataTable = $("#student_table").DataTable({
                pageLength: 10,
                scrollX: true,
                lengthChange: false,
                'columnDefs': [{
                    'targets': [1, 2, 3, 4],
                    'orderable': true,
                }],
                'columnDefs': [{
                    'targets': [5],
                    'orderable': false,
                }]
            });

            $('#importExcelForm').on('submit', function (e) {
                e.preventDefault();

                const importBtn = $(this).find('button[type="submit"]');
                importBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Importing...');

                var formData = new FormData(this);

                $.ajax({
                    url: './import_students.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log("Raw response:", response);

                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            console.log("Parsed data:", data);
                            if (data.success) {
                                showAlert2(data.message, 'success');
                                $('#importExcelModal').modal('hide');
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                showAlert2(data.message, 'danger');
                            }
                        } catch (e) {
                            showAlert2('An error occurred while processing the response.', 'danger');
                            console.error('Parsing error:', e, response);
                        }
                    },
                    error: function (xhr, status, error) {
                        showAlert2('An error occurred: ' + error, 'danger');
                        console.error('AJAX error:', error);
                    },
                    complete: function () {
                        importBtn.prop('disabled', false).text('Import');
                    }
                });
            });
        });


        function showAlert1(message, type) {
            const alertContainer = document.getElementById('alertContainer1');
            const alertHTML = `
                    <div class="alert alert-${type} d-flex flex-row align-items-center gap-2 position-fixed top-0 start-50 translate-middle-x w-auto mt-4 z-index-1050" role="alert">
                        <strong>${type === 'success' ? `Success! <i class='bx bx-check-circle'></i>` : 'Error!'}</strong> ${message}
                    </div>
                `;
            alertContainer.innerHTML = alertHTML;

            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        function showAlert2(message, type) {
            const alertContainer = document.getElementById('alertContainer2');
            const alertHTML = `
                    <div class="alert alert-${type} d-flex flex-row align-items-center gap-2 position-fixed bottom-0 start-50 translate-middle-x w-auto mt-4 z-index-1050" role="alert">
                        <strong>${type === 'success' ? `Success! <i class='bx bx-check-circle'></i>` : 'Error!'}</strong> ${message}
                    </div>
                `;
            alertContainer.innerHTML = alertHTML;

            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            let gradesId = null;

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    gradesId = this.getAttribute('data-subject-id');
                    deleteModal.show();
                });
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                if (gradesId) {
                    fetch('./delete_grades.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ grades_id: gradesId }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlert1(data.message, 'success');
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
        });
    </script>
</body>

</html>