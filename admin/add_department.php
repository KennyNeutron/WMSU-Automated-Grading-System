<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

require_once '../tools/functions.php';
require_once '../classes/department.class.php';

$error_message = '';
$success = false;

if (isset($_POST['confirm_add'])) {
    $department_name = $_POST['department_name'];

    if (empty($department_name)) {
        $error_message = "Department name is required.";
    } else {
        $department = new Department();

        if ($department->add($department_name)) {
            $success = true;
            $message = "Department added";
        } else {
            $error_message = "Failed to add department.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Add Department';
$department_page = 'active';
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
                    <a role="button" href="./manage_department.php" class="bg-none d-flex align-items-center"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></a>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Add Department</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <form action="" method="POST">
                    <?php if (!empty($errors_message)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error_message) ?> 
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success gap-2">
                            <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?> successfully!
                        </div>
                    <?php endif; ?>

                    <div class="w-25" id="pw_input_toggle">
                        <div class="mb-3">
                            <label for="department_name" class="form-label">Department Name</label>
                            <div class="input-group">
                                <span class="input-group-text">Department of </span>
                                <input type="text" class="form-control" id="department_name" name="department_name"
                                    value="<?php if (isset($_POST['department_name']))
                                        echo htmlspecialchars($_POST['department_name']); ?>">
                            </div>
                        </div>
                        <div class="d-flex justify-content-start gap-2">
                            <button type="submit" name="confirm_add" class="btn brand-bg-color"
                                id="confirm_add">Confirm</button>
                        </div>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = './manage_department.php';
            }, 1500);
        <?php endif; ?>
    </script>

</body>

</html>