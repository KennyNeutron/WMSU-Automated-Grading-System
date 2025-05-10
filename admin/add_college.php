<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

require_once '../tools/functions.php';
require_once '../classes/department.class.php';
require_once '../classes/college.class.php';

$error_message = '';
$success = false;

$department = new Department();
$college = new College();
$existing_departments = $department->show(); // Fetch all existing departments

if (isset($_POST['confirm_add'])) {
    $college_name = ucwords($_POST['college_name']);
    $selected_departments = isset($_POST['departments']) ? $_POST['departments'] : [];

    if (empty($college_name)) {
        $error_message = "College name is required.";
    } else {
        $departments = implode(',', $selected_departments); // Convert array to comma-separated string

        if ($college->add($college_name, $departments)) {
            $success = true;
            $message = "College added";
        } else {
            $error_message = "Failed to add college.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Add College';
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
                    <a role="button" href="./manage_college.php" class="bg-none d-flex align-items-center"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></a>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Add College</span>
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

                    <div class="w-50" id="pw_input_toggle">
                        <!-- College Name Field -->
                        <div class="mb-3">
                            <label for="college_name" class="form-label">College Name</label>
                            <div class="input-group">
                                <span class="input-group-text">College of </span>
                                <input type="text" class="form-control" id="college_name" name="college_name"
                                    value="<?= htmlspecialchars($_POST['college_name'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Departments Dropdown -->
                        <div class="mb-3">
                            <label for="departments" class="form-label">Select Departments</label>
                            <select class="form-select" id="departments" name="departments[]" multiple>
                                <?php foreach ($existing_departments as $dept): ?>
                                    <option value="<?= $dept['department_id'] ?>" 
                                        <?= (isset($_POST['departments']) && in_array($dept['department_id'], $_POST['departments'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dept['department_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-start gap-2">
                            <button type="submit" name="confirm_add" class="btn brand-bg-color" id="confirm_add">Confirm</button>
                        </div>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        new MultiSelectTag('departments', {
            rounded: true,    // default true
            shadow: true,      // default false
            placeholder: 'Search Department',  // default Search...
            tagColor: {
                textColor: '#952323',
                borderColor: '#dd3838',
                bgColor: '#e59d9d',
            },
            onChange: function(values) {
                console.log(values)
            }
        }) 

        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = './manage_college.php';
            }, 1500);
        <?php endif; ?>
    </script>

</body>

</html>
