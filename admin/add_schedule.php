<?php
session_start();

require_once '../tools/functions.php';
require_once '../classes/profiling.class.php';
require_once '../classes/faculty_sched.class.php';
require_once '../classes/semester.class.php';

$semester = new Semester();
$profiling = new Profiling();
$sched = new Faculty_Sched();
$semesterarray = $semester->show();
$profile = $profiling->fetchAll();
$error_message = '';
$success = false;

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

if (isset($_POST['add_sched'])) {
    // Collecting data from the form
    $profiling_id = htmlentities($_POST['profiling_id']);
    $release_time = htmlentities($_POST['release_time']);
    $hrs_per_week = htmlentities($_POST['hrs_per_week']);
    $school_yr = htmlentities($_POST['school_yr'] . ' - ' . $_POST['school_yr2']);
    $semester = htmlentities($_POST['semester']);

    // Set the values for the sched object
    $sched->release_time = $release_time;
    $sched->hrs_per_week = $hrs_per_week;
    $sched->school_yr = $school_yr;
    $sched->semester = $semester;

    // Call the add function with the profiling_id
    if ($sched->add($profiling_id)) {
        $message = 'Schedule added';
        $success = true;
    } else {
        $error_message = 'Something went wrong adding schedule.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Add Schedule';
$profiling_page = 'active';
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
                    <button onclick="history.back()" class="bg-none d-flex align-items-center"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></button>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Add Schedule</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <form action="#" method="post">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success gap-2">
                            <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?> successfully!
                        </div>
                    <?php endif; ?>

                    <div class="row row-cols-1 row-cols-md-2">
                        <div class="col">
                            <div class="mb-3">
                                <label for="profiling_id" class="form-label">Select Employee</label>
                                <?php
                                // Assuming you already have this from the previous code
                                $department_id_from_get = $_GET['department_id'];
                                ?>
                                <select class="form-select" id="profiling_id" name="profiling_id"
                                    onchange="populateEmployeeDetails(this)">
                                    <option value="" disabled selected>Select Employee</option>
                                    <?php
                                    foreach ($profile as $user) {
                                        // Check if the department_id from the profile matches the department_id from GET
                                        if ($user['department_id'] == $department_id_from_get) {
                                            $full_name = $user['f_name'] . ' ' . $user['m_name'] . ' ' . $user['l_name'];
                                            echo "<option value='" . htmlspecialchars($user['profiling_id']) . "' 
                                                    data-email='" . htmlspecialchars($user['email']) . "' 
                                                    data-fname='" . htmlspecialchars($user['f_name']) . "' 
                                                    data-mname='" . htmlspecialchars($user['m_name']) . "' 
                                                    data-lname='" . htmlspecialchars($user['l_name']) . "' 
                                                    data-designation='" . htmlspecialchars($user['designation']) . "' 
                                                    data-facultytype='" . htmlspecialchars($user['faculty_type']) . "' 
                                                    data-acadtype='" . htmlspecialchars($user['acad_type']) . "' 
                                                    data-department='" . htmlspecialchars($user['department_name']) . "'>
                                                    $full_name (" . htmlspecialchars($user['emp_id']) . ")
                                                </option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="f_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="f_name" name="f_name" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="faculty_type" class="form-label">Faculty Type</label>
                                <input type="text" class="form-control" id="faculty_type" name="faculty_type" readonly>
                            </div>
                        </div>

                        <div class="col">

                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department_id" name="department_id"
                                    readonly>
                            </div>


                            <div class="mb-3">
                                <label for="acad_type" class="form-label">Academic Rank</label>
                                <input type="text" class="form-control" id="acad_type" name="acad_type" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="designation" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="designation" name="designation" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row row-cols-1 row-cols-md-2">
                        <div class="col">
                            <div class="mb-3">
                                <label for="release_time" class="form-label">Release Time (With/Without)</label>
                                <select type="button" type="button" class="dropdown-toggle form-select"
                                    data-bs-toggle="dropdown" id="release_time" name="release_time" required>
                                    <option value="" selected disabled>Select Release Time</option>
                                    <option value="0">Without Release Time</option>
                                    <option value="1">With Release Time</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="hrs_per_week" class="form-label">Number of Hours per Week</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="hrs_per_week" name="hrs_per_week"
                                        required>
                                    <span class="input-group-text">hrs</span>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="mb-3">
                                <label for="school_yr" class="form-label">School Year</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="school_yr" name="school_yr"
                                        placeholder="YYYY" required>
                                    <span class="input-group-text">-</span>
                                    <input type="text" class="form-control" id="school_yr2" name="school_yr2"
                                        placeholder="YYYY" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="semester" class="form-label">Semester</label>
                                <select type="button" type="button" class="dropdown-toggle form-select"
                                    data-bs-toggle="dropdown" id="semester" name="semester" required>
                                    <option value="" selected disabled>Select Semester</option>
                                    <?php
                                    foreach ($semesterarray as $semesterItem) {
                                        $semesterName = htmlspecialchars($semesterItem['semester']);
                                        $semesterId = htmlspecialchars($semesterItem['semester_id']);
                                        echo "<option value='$semesterId'>$semesterName</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a onclick="history.back()" class="btn btn-secondary">Cancel</a>
                        <button type="submit" name="add_sched" class="btn brand-bg-color">Submit</button>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        $(document).ready(function () {
            $('#profiling_id').select2({
                placeholder: "Type to search for an employee",
                allowClear: true,
                width: '100%',
            });
        });

        $(document).ready(function () {
            $('#school_yr').on('input', function () {
                var startYear = parseInt($(this).val());
                if (!isNaN(startYear)) {
                    $('#school_yr2').attr('value', startYear + 1);
                }
            });
        });

        function capitalizeFirstLetter(input) {
            input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
        }

        function populateEmployeeDetails(select) {
            const selectedOption = select.options[select.selectedIndex];
            const fname = selectedOption.getAttribute('data-fname');
            const mname = selectedOption.getAttribute('data-mname');
            const lname = selectedOption.getAttribute('data-lname');
            const email = selectedOption.getAttribute('data-email');
            const facultyType = selectedOption.getAttribute('data-facultytype');
            const designation = selectedOption.getAttribute('data-designation');
            const acadType = selectedOption.getAttribute('data-acadtype');
            const department = selectedOption.getAttribute('data-department');
            const empId = selectedOption.value;

            // Populate input fields
            document.getElementById('f_name').value = `${lname || ''}, ${fname || ''} ${mname || ''}`.trim();
            document.getElementById('email').value = email || '';
            document.getElementById('faculty_type').value = facultyType || '';
            document.getElementById('designation').value = designation || '';
            document.getElementById('acad_type').value = acadType || '';
            document.getElementById('department_id').value = department || '';
            document.getElementById('profiling_id').value = empId;
        }


        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = './faculty.php?department_id=<?= $_GET['department_id'] ?>';
            }, 1500);
        <?php endif; ?>
    </script>

</body>

</html>