<?php
session_start();

// Redirect if user is not logged in or not an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    header('location: ../login');
    exit();
}

require_once '../tools/functions.php';
require_once '../classes/course_select.class.php';

$success = false;
$errors = '';
$message = '';

$program_id = $_GET['college_course_id'];
$program = new Course_curr();
$program_details = $program->getCourseNameById($program_id);

if (!$program_details) {
    $errors = 'Invalid program ID.';
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_program'])) {
    $program_name = trim($_POST['program_name']);
    $degree_level = $_POST['degree_level'] ?? '';

    // Validate inputs
    if (empty($program_name)) {
        $errors = 'Program name is required.';
    } elseif (empty($degree_level)) {
        $errors = 'Please select a degree level.';
    } else {
        // Update the program
        $program->college_course_id = $program_id;
        $program->name = ucwords(strtolower($_POST['program_name']));
        $program->degree_level = $_POST['degree_level'];

        if ($program->update()) {
            $success = true;
            $message = 'Program successfully updated.';
        } else {
            $errors = 'Failed to update the program. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Edit Program';
$curriculum_page = 'active';
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
                    <button onclick="history.back()" class="bg-none"><i class='bx bx-chevron-left fs-2 brand-color'></i></button>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Edit Program</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
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

                <form action="" method="POST">
                    <div class="row row-cols-1 row-cols-md-2">
                        <div class="col">
                            <div class="mb-3">
                                <label for="program_name" class="form-label">Program Name</label>
                                <input type="text" class="form-control" id="program_name" name="program_name" 
                                    value="<?= htmlspecialchars($_POST['program_name'] ?? $program_details['name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="degree_level" class="form-label">Degree Level</label>
                                <input type="text" class="form-control" id="degree_level" name="degree_level" 
                                    value="<?= $program_details['degree_level'] ?>" readonly>
                                <!-- <select class="form-select" id="degree_level" name="degree_level" required>
                                    <option value="" disabled>Select degree level</option>
                                    <option value="Associate Degree" <?= ($program_details['degree_level'] == 'Associate Degree') ? 'selected' : '' ?>>Associate Degree</option>
                                    <option value="Bachelor's Degree" <?= ($program_details['degree_level'] == "Bachelor's Degree") ? 'selected' : '' ?>>Bachelor's Degree</option>
                                    <option value="Master's Degree" <?= ($program_details['degree_level'] == "Master's Degree") ? 'selected' : '' ?>>Master's Degree</option>
                                    <option value="Doctoral's Degree" <?= ($program_details['degree_level'] == "Doctoral's Degree") ? 'selected' : '' ?>>Doctoral's Degree</option>
                                </select> -->
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="history.back()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn brand-bg-color" name="edit_program"><i
                    class='bx bxs-save me-2'></i>Save Changes</button>
                </form>
            </div>
        </main>
    </div>

    <script src="./js/main.js"></script>
    <?php if ($success): ?>
        <script>
            setTimeout(function () {
              window.location.href = "./course_select?curr_year_id=<?= $_GET['curr_year_id']?>";
            }, 1500);
        </script>
    <?php endif; ?>
</body>
</html>
