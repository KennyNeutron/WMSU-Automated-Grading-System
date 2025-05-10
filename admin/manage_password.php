<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

require_once '../tools/functions.php';
require_once '../classes/user.class.php';

$user_id = $_SESSION['user_id'];
$error_message = '';
$success = false;

$user = new User();
$record = $user->fetch($user_id);
$user->user_id = $user_id;

if (isset($_POST['save_password'])) {
    try {
        $current_password = $_POST['current_pw'] ?? '';
        $new_password = $_POST['new_pw'] ?? '';
        $confirm_password = $_POST['confirm_pw'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = "All fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $error_message = "New passwords do not match.";
        } elseif (strlen($new_password) < 8) {
            $error_message = "New password must be at least 8 characters long.";
        } else {
            if ($user->changePassword($user_id, $current_password, $new_password)) {
                $success = true;
                $message = "Password changed successfully.";
            } else {
                $error_message = "Incorrect current password or failed to update password.";
            }
        }
    } catch (Exception $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Settings';
$setting_page = 'active';
include '../includes/admin_head.php';
?>

<body>
    <div class="home">
        <?php if ($success): ?>
            <div
                class="alert alert-success alert-dismissible fade show d-flex flex-row align-items-center gap-2 position-fixed bottom-0 end-0 mb-4 me-4 w-auto z-index-1050">
                <?= $message ?> successfully!
                <i class='bx bx-check-circle'></i>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div
                class="alert alert-danger alert-dismissible fade show d-flex flex-row align-items-center gap-2 position-fixed bottom-0 end-0 mb-4 me-4 w-auto z-index-1050">
                <?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
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
                    <a role="button" href="./admin_settings.php" class="bg-none d-flex align-items-center"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></a>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Settings</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <form action="" method="POST">
                    <div class="container-fluid d-flex justify-content-start">
                        <span class="fs-2 fw-bold h1 m-0 brand-color mb-3">Change Password</span>
                    </div>
                    <div class="w-25" id="pw_input_toggle">
                        <div class="mb-3">
                            <label for="current_pw" class="form-label">Current Password</label>
                            <div class="input-group gap-2">
                                <input type="password" class="form-control" id="current_pw" name="current_pw" value="<?php if (isset($_POST['current_pw']))
                                    echo htmlspecialchars($_POST['current_pw']); ?>">
                                <button type="button" class="toggle-password" style="background: none; color: black;"
                                    onclick="togglePasswordVisibility('current_pw', this)">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="new_pw" class="form-label">New Password</label>
                            <div class="input-group gap-2">
                                <input type="password" class="form-control" id="new_pw" name="new_pw" value="<?php if (isset($_POST['new_pw']))
                                    echo htmlspecialchars($_POST['new_pw']); ?>">
                                <button type="button" class="toggle-password" style="background: none; color: black;"
                                    onclick="togglePasswordVisibility('new_pw', this)">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_pw" class="form-label">Confirm New Password</label>
                            <div class="input-group gap-2">
                                <input type="password" class="form-control" id="confirm_pw" name="confirm_pw" value="<?php if (isset($_POST['confirm_pw']))
                                    echo htmlspecialchars($_POST['confirm_pw']); ?>">
                                <button type="button" class="toggle-password" style="background: none; color: black;"
                                    onclick="togglePasswordVisibility('confirm_pw', this)">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start gap-2">
                            <button type="submit" name="save_password" class="btn brand-bg-color"
                                id="changePasswordButton" disabled>Change Password</button>
                        </div>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        const passwordInputs = document.querySelectorAll("#current_pw, #new_pw, #confirm_pw");
        const changePasswordButton = document.getElementById("changePasswordButton");

        passwordInputs.forEach((input) => {
            input.addEventListener("input", () => {
                const allFilled = Array.from(passwordInputs).every(field => field.value.trim() !== "");
                changePasswordButton.disabled = !allFilled;
            });
        });

        function togglePasswordVisibility(fieldId, toggleButton) {
            const passwordField = document.getElementById(fieldId);
            const isPasswordVisible = passwordField.type === 'text';
            passwordField.type = isPasswordVisible ? 'password' : 'text';

            const icon = toggleButton.querySelector('i');
            icon.classList.toggle('bx-show');
            icon.classList.toggle('bx-hide');
        }

        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = './admin_settings.php';
            }, 1500);
        <?php endif; ?>
    </script>

</body>

</html>