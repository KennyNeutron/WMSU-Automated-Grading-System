<?php
session_start();

require_once '../tools/functions.php';
require_once '../classes/profiling.class.php';
require_once '../classes/faculty_sched.class.php';
require_once '../classes/semester.class.php';

// Check user role
if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

// Initialize classes
$semester = new Semester();
$profiling = new Profiling();
$sched = new Faculty_Sched();

// Check if sched_id is provided in the URL
if (isset($_GET['sched_id'])) {
    $sched_id = $_GET['sched_id'];
    $existingSched = $sched->fetch($sched_id);

    if (!$existingSched) {
        $error_message = "Schedule not found.";
    }
} else {
    $error_message = "Invalid schedule ID.";
}

// Get semester and profile data
$semesterarray = $semester->show();
$profile = $profiling->fetchForSched($existingSched['profiling_id']);

$error_message = '';
$success = false;



// Handle form submission
if (isset($_POST['edit_sched']) && isset($sched_id)) {
    // Collect data from the form
    $profiling_id = htmlentities($_POST['profiling_id']);
    $release_time = htmlentities($_POST['release_time']);
    $hrs_per_week = htmlentities($_POST['hrs_per_week']);
    $school_yr = htmlentities($_POST['school_yr'] . ' - ' . $_POST['school_yr2']);
    $semester = htmlentities($_POST['semester']);

    // Update the schedule object
    $sched->sched_id = $sched_id;
    $sched->profiling_id = $profiling_id;
    $sched->release_time = $release_time;
    $sched->hrs_per_week = $hrs_per_week;
    $sched->school_yr = $school_yr;
    $sched->semester = $semester;

    // Update the schedule in the database
    if ($sched->edit()) {
        $message = 'Schedule updated';
        $success = true;
    } else {
        $error_message = 'Something went wrong updating the schedule.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Edit Schedule';
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
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Edit Schedule</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <form action="#" method="post">
                    <?php if (!empty($error_message)): ?>
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
                                <label for="profiling_id" class="form-label">Employee</label>
                                <input type="text" class="form-control" id="f_name" name="f_name" readonly
                                    value="<?= htmlspecialchars($profile['fullName'] . " (" . $profile['emp_id'] . ")" ?? '') ?>">
                                <input type="hidden" class="form-control" id="profiling_id" name="profiling_id" readonly
                                    value="<?= htmlspecialchars($existingSched['profiling_id'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" readonly
                                    value="<?= htmlspecialchars($profile['email'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="faculty_type" class="form-label">Faculty Type</label>
                                <input type="text" class="form-control" id="faculty_type" name="faculty_type" readonly
                                    value="<?= htmlspecialchars($profile['faculty_type'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col">
                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department_id" name="department_id" readonly
                                    value="<?= htmlspecialchars($profile['department_name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="designation" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="designation" name="designation" readonly
                                    value="<?= htmlspecialchars($profile['designation'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="acad_type" class="form-label">Academic Rank</label>
                                <input type="text" class="form-control" id="acad_type" name="acad_type" readonly
                                    value="<?= htmlspecialchars($profile['acad_type'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row row-cols-1 row-cols-md-2">
                        <div class="col">
                            <div class="mb-3">
                                <label for="release_time" class="form-label">Release Time (With/Without)</label>
                                <select class="form-select" id="release_time" name="release_time" required>
                                    <option value="0" <?= $existingSched['release_time'] == '0' ? 'selected' : '' ?>>
                                        Without Release Time</option>
                                    <option value="1" <?= $existingSched['release_time'] == '1' ? 'selected' : '' ?>>
                                        With Release Time</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="hrs_per_week" class="form-label">Number of Hours per Week</label>
                                <input type="number" class="form-control" id="hrs_per_week" name="hrs_per_week" required
                                    value="<?= htmlspecialchars($existingSched['hrs_per_week'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col">
                            <div class="mb-3">
                                <label for="school_yr" class="form-label">School Year</label>
                                <?php
                                $school_yr_parts = explode(" - ", $existingSched['school_yr']);
                                $school_yr1 = isset($school_yr_parts[0]) ? $school_yr_parts[0] : '';
                                $school_yr2 = isset($school_yr_parts[1]) ? $school_yr_parts[1] : '';
                                ?>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="school_yr" name="school_yr"
                                        placeholder="YYYY" value="<?= htmlspecialchars($school_yr1) ?>" required>
                                    <span class="input-group-text">-</span>
                                    <input type="text" class="form-control" id="school_yr2" name="school_yr2"
                                        placeholder="YYYY" value="<?= htmlspecialchars($school_yr2) ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="semester" class="form-label">Semester</label>
                                <select class="form-select" id="semester" name="semester" required>
                                    <option value="" disabled>Select Semester</option>
                                    <?php
                                    foreach ($semesterarray as $semesterItem):
                                        $semesterName = htmlspecialchars($semesterItem['semester']);
                                        $semesterId = htmlspecialchars($semesterItem['semester_id']);
                                        ?>
                                        <option value='<?= $semesterId ?>'
                                            <?= $semesterItem['semester_id'] == $existingSched['semester'] ? 'selected' : '' ?>> <?= $semesterName ?> </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a onclick="history.back()" class="btn btn-secondary">Cancel</a>
                        <button class="btn btn-success d-flex align-items-center" type="submit" name="edit_sched"><i
                                class='bx bxs-save me-2'></i>Save Changes</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../js/employee.js"></script>
    <script>
        $(document).ready(function () {
            $('#school_yr').on('input', function () {
                var startYear = parseInt($(this).val());
                if (!isNaN(startYear)) {
                    $('#school_yr2').attr('value', startYear + 1);
                }
            });
        });
        
        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = './faculty.php?department_id=<?= $_GET['department_id'] ?>';
            }, 1500);
        <?php endif; ?>
    </script>
</body>

</html>