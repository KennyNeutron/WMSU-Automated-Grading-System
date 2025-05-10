<?php
session_start();

// Redirect if user is not logged in or not an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    header('location: ../login');
    exit();
}

require_once '../tools/functions.php';
require_once '../classes/students.class.php';
require_once '../classes/grades.class.php';
require_once '../classes/faculty_subs.class.php';

$fac_subs = new Faculty_Subjects();
$student = new Students();
$grades = new Grades();

$subject = $fac_subs->getProf($_GET['faculty_sub_id']);
$existingStudents = $student->show();

$success = false;
$errors = '';
$message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $student_id = trim($_POST['student_id']);
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $year_section = trim($_POST['year_section']);
    $selectedStudentId = trim($_POST['existing_student_id'] ?? '');

    // Validate inputs
    if (empty($selectedStudentId) && empty($student_id)) {
        $errors = 'Student ID is required.';
    } elseif (empty($firstname) && empty($selectedStudentId)) {
        $errors = 'First name is required.';
    } elseif (empty($lastname) && empty($selectedStudentId)) {
        $errors = 'Last name is required.';
    } elseif (!empty($email) && !preg_match('/^[a-zA-Z0-9._%+-]+@wmsu\.edu\.ph$/', $email)) {
        $errors = 'Only @wmsu.edu.ph emails are allowed.';
    } elseif (empty($year_section) && empty($selectedStudentId)) {
        $errors = 'Year and section are required.';
    } else {
        if (!empty($selectedStudentId)) {
            // Use selected student from dropdown
            $existingStudentBySub = $grades->getStudentByIdAndSub($selectedStudentId, $_GET['faculty_sub_id']);

            if ($existingStudentBySub) {
                $errors = 'Selected student already exists in ' . $subject['sub_code'] . '.';
            } else {
                // Add to grades table
                $grades->student_data_id = $selectedStudentId;
                $grades->faculty_sub_id = htmlentities($_GET['faculty_sub_id']);

                if ($grades->add()) {
                    $success = true;
                    $message = 'Student successfully added.';
                } else {
                    $errors = 'Failed to add the student.';
                }
            }
        } else {
            $existingStudent = $student->getStudentById($student_id);

            if ($existingStudent) {
                $errors = 'Student ID already exists.';
            } else {
                // Add new student
                $student->faculty_sub_id = htmlentities($_GET['faculty_sub_id']);
                $student->student_id = htmlentities($student_id);
                $student->student_firstname = ucwords(strtolower(htmlentities($firstname)));
                $student->student_middlename = !empty($middlename) ? ucwords(strtolower(htmlentities($middlename))) : '';
                $student->student_lastname = ucwords(strtolower(htmlentities($lastname)));
                $student->email = htmlentities($email);
                $student->year_section = htmlentities($year_section);
                $student->suffix = htmlentities($_POST['suffix'] ?? null);

                if ($student->add()) {
                    $newStudent = $student->getStudentById($student_id);

                    if ($newStudent) {
                        $grades->student_data_id = $newStudent['student_data_id'];
                        $grades->faculty_sub_id = htmlentities($_GET['faculty_sub_id']);

                        if ($grades->add()) {
                            $success = true;
                            $message = 'New student successfully added.';
                        } else {
                            $errors = 'Failed to add the new student to grades.';
                        }
                    } else {
                        $errors = 'Failed to retrieve the new student data.';
                    }
                } else {
                    $errors = 'Failed to add the new student.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Add Student';
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
                    <button onclick="history.back()" class="bg-none"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></button>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Add Student</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <form action="" method="POST">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success gap-2">
                            <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>
                    <div class="row row-cols-1 row-cols-md-2">
                        <div class="col">
                            <div class="mb-3">
                                <label for="existing_student_id" class="form-label">Select Existing Student</label>
                                <select class="form-select" id="existing_student_id" name="existing_student_id"
                                    onchange="populateEmployeeDetails(this)">
                                    <option value="" disabled selected>Select an existing student</option>
                                    <?php foreach ($existingStudents as $existingStudent): ?>
                                        <option value="<?= htmlspecialchars($existingStudent['student_data_id']) ?>"
                                            data-student_id="<?= htmlspecialchars($existingStudent['student_id']) ?>"
                                            data-firstname="<?= htmlspecialchars($existingStudent['student_firstname']) ?>"
                                            data-middlename="<?= htmlspecialchars($existingStudent['student_middlename']) ?>"
                                            data-lastname="<?= htmlspecialchars($existingStudent['student_lastname']) ?>"
                                            data-suffix="<?= htmlspecialchars($existingStudent['suffix']) ?>"
                                            data-email="<?= htmlspecialchars($existingStudent['email']) ?>"
                                            data-year_section="<?= htmlspecialchars($existingStudent['year_section']) ?>">
                                            <?= htmlspecialchars(ucwords(strtolower($existingStudent['student_lastname'] . $existingStudent['suffix']))) ?>,
                                            <?= htmlspecialchars(ucwords(strtolower($existingStudent['student_firstname']))) ?>
                                            (<?= htmlspecialchars($existingStudent['student_id']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="student_id" placeholder="eg. 202105376"
                                    name="student_id" value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstname" placeholder="eg. Juan Robert"
                                    name="firstname" value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="middlename" class="form-label">* Middle Name</label>
                                <input type="text" class="form-control" id="middlename" placeholder="eg. Dela"
                                    name="middlename" value="<?= htmlspecialchars($_POST['middlename'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastname" placeholder="eg. Cruz"
                                    name="lastname" value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="suffix" class="form-label">* Suffix</label>
                                <select class="form-select" id="suffix" name="suffix">
                                    <option value="">Select suffix</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                    <option value="V">V</option>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="eg. xt202511235@wmsu.edu.ph"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="year_section" class="form-label">Year and Section</label>
                                <input type="text" class="form-control" id="year_section" placeholder="eg. BEED 2A"
                                    name="year_section"
                                    value="<?= htmlspecialchars($subject['yr_sec'] ?? $_POST['year_section'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" onclick="history.back()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn brand-bg-color" name="add_student">Add</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        $(document).ready(function () {
            $('#existing_student_id').select2({
                placeholder: "Select existing student",
                allowClear: true,
                width: '100%',
            });
        });

        function populateEmployeeDetails(select) {
            const selectedOption = select.options[select.selectedIndex];
            const student_id = selectedOption.getAttribute('data-student_id');
            const firstname = selectedOption.getAttribute('data-firstname');
            const middlename = selectedOption.getAttribute('data-middlename');
            const lastname = selectedOption.getAttribute('data-lastname');
            const email = selectedOption.getAttribute('data-email');
            const suffix = selectedOption.getAttribute('data-suffix');
            const year_section = selectedOption.getAttribute('data-year_section');

            document.getElementById('student_id').value = student_id || '';
            document.getElementById('firstname').value = firstname || '';
            document.getElementById('middlename').value = middlename || '';
            document.getElementById('lastname').value = lastname || '';
            document.getElementById('email').value = email || '';
            document.getElementById('suffix').value = suffix || '';
            document.getElementById('year_section').value = year_section || '';
        }

        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = "./sub_students?sched_id=<?= $_GET['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>&faculty_sub_id=<?= $_GET['faculty_sub_id'] ?>"
            }, 1500);
        <?php endif; ?>
    </script>

</body>

</html>