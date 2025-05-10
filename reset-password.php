<?php
session_start();

if (!isset($_SESSION['verification_id']) || empty($_SESSION['verification_id'])) {
  header('Location: ./login.php');
  exit;
}

require_once './tools/functions.php';
require_once './classes/user.class.php';
require_once './classes/notification.class.php';
$error = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
  $user = new User();
  $notification = new Notification();
  $user->password = htmlentities($_POST['password']);
  $confirm_password = trim($_POST['confirm_password']);
  ;

  if (!isset($_POST['password']) || empty($confirm_password)) {
    $error = "Please fill out both password fields.";
  } elseif ($_POST['password'] !== $confirm_password) {
    $error = "Passwords do not match.";
  } elseif (strlen($_POST['password']) < 8) {
    $error = "Password must be at least 8 characters long.";
  } else {
    if ($user->update_pass($_SESSION['verification_id'])) {
      $userInfo = $user->fetch($_SESSION['verification_id']);
      if ($userInfo) {
        $email = $userInfo['email'];
        $emp_id = $userInfo['emp_id'];

        $message = "The account <span style='color:blue;'>$email($emp_id)</span> has successfully changed its password.";

        $notification = new Notification();
        $notification->user_role = 2;
        $notification->message = $message;

        if ($notification->add()) {
          $success = true;
          session_destroy();
        } else {
          $error = "Password updated, but notification could not be added.";
        }
      }
    } else {
      $error = "Failed to update the password. Please try again.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Reset Password';
include_once './includes/head.php'
  ?>

<body class="login">
  <main>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
      <div class="login-page p-4">
        <p class="text-center">
          <img src="./img/wmsu_logo.png" alt="wmsu-logo" class="img-fluid">
        </p>
        <h1 class="fs-1 fw-bold my-3 mb-4 text-white text-center brand-color">Reset Password</h1>
        <?php if ($error): ?>
          <div class="alert alert-danger">
            <?= $error ?>
          </div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="alert alert-success">
            Password reset successful!
          </div>
        <?php else: ?>
          <form action="#" method="post">
            <div class="field">
              <i class='bx bxs-lock-alt'></i>
              <input type="password" name="password" id="password" required value="<?php if (isset($_POST['password'])) {
                echo $_POST['password'];
              } ?>">
              <?php
              if (isset($_POST['password']) && !validate_field($_POST['password'])) {
                ?>
                <span>Enter your Password</span>
                <?php
              }
              ?>
              <button type="button" class="toggle-password" style="background: none; color: white;"
                onclick="togglePasswordVisibility('password', this)">
                <i class="bx bx-show"></i>
              </button>
              <label for="password">Password</label>
            </div>

            <div class="field">
              <i class='bx bxs-check-square'></i>
              <input type="password" name="confirm_password" id="confirm_password" required value="<?php if (isset($_POST['confirm_password'])) {
                echo $_POST['confirm_password'];
              } ?>">
              <?php
              if (isset($_POST['confirm_password']) && !validate_field($_POST['confirm_password'])) {
                ?>
                Confirm your Password
                <?php
              }
              ?>
              <button type="button" class="toggle-password" style="background: none; color: white;"
                onclick="togglePasswordVisibility('confirm_password', this)">
                <i class="bx bx-show"></i>
              </button>
              <label for="confirm_password">Confirm Password</label>
            </div>

            <button type="submit" name="reset" class="btn d-flex p-2 p-sm-3 justify-content-center">Confirm</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <script>
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
        window.location.href = './login.php';
      }, 1500);
    <?php endif; ?>
  </script>
</body>

</html>