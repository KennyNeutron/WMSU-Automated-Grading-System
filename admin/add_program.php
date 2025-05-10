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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_program'])) {
    $program_name = trim($_POST['program_name']);
    $degree_level = $_POST['degree_level'] ?? '';

    // Validate inputs
    if (empty($program_name)) {
        $errors = 'Program name is required.';
    } elseif (empty($degree_level)) {
        $errors = 'Please select a degree level.';
    } else {
        // Save the program
        $program = new Course_curr();
        $program->name = ucwords(strtolower(htmlentities($program_name)));
        $program->degree_level = $degree_level;

        if ($program->add()) {
            $success = true;
            $message = 'Program successfully added.';
        } else {
            $errors = 'Failed to add the program. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Add Program';
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
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Add Program</span>
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
                                <label for="program_name" class="form-label">Program Name</label>
                                <input type="text" class="form-control" placeholder="eg. Civil Engineering" id="program_name"
                                    name="program_name" value="<?= htmlspecialchars($_POST['program_name'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="degree_level" class="form-label">Degree Level</label>
                                <select class="form-select" id="degree_level" name="degree_level" required>
                                    <option value="" disabled selected>Select degree level</option>
                                    <option value="Associate Degree" <?= (isset($_POST['degree_level']) && $_POST['degree_level'] == 'Associate Degree') ? 'selected' : '' ?>>Associate Degree</option>
                                    <option value="Bachelor's Degree" <?= (isset($_POST['degree_level']) && $_POST['degree_level'] == "Bachelor's Degree") ? 'selected' : '' ?>>Bachelor's Degree</option>
                                    <option value="Master's Degree" <?= (isset($_POST['degree_level']) && $_POST['degree_level'] == "Master's Degree") ? 'selected' : '' ?>>Master's Degree</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="history.back()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn brand-bg-color" name="add_program">Add</button>
                </form>
            </div>
        </main>
    </div>

    <script src="./js/main.js"></script>
    <?php if ($success): ?>
        <script>
            setTimeout(function () {
              window.location.href = "./course_select?curr_year_id=<?= $_GET['curr_year_id'] ?>"
            }, 1500);
        </script>
    <?php endif; ?>
</body>
</html>