<?php
session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1) {
  header('location: ./index.php');
} else if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 2) {
  header('location: ./admin/index.php');
}

require_once './tools/functions.php';
require_once './classes/user.class.php';

$message = '';
$signup_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = new User();
  $user->user_role = isset($_POST['user_role']) ? htmlentities($_POST['user_role']) : 1;
  $user->emp_id = htmlentities($_POST['emp_id']);
  $user->email = htmlentities($_POST['email']);
  $user->password = htmlentities($_POST['password']);
  $user->f_name = htmlentities($_POST['f_name']);
  $user->l_name = htmlentities($_POST['l_name']);
  $user->m_name = htmlentities($_POST['m_name']);
  $user->faculty_type = htmlentities($_POST['faculty_type']);

  if ($user->is_emp_id_exist()) {
    $message = 'Employee ID already exists.';
  } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@wmsu\.edu\.ph$/', $_POST['email'])) {
    $message = 'Only @wmsu.edu.ph emails are allowed.';
  } else if ($user->is_email_exist()) {
    $message = 'Email already exists.';
  } else if (strlen($_POST['password']) < 8) {
    $message = "Password must be at least 8 characters long.";
  } else {
    if ($user->add()) {
      $message = 'Signup successful! Redirecting to login page...';
      $signup_success = true;
    } else {
      $message = 'An error occurred during registration. Please try again.';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Signup';
include_once './includes/head.php'
  ?>

<body class="signup">
  <main>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
      <div class="signup-page">
        <p class="text-center">
          <img src="./img/wmsu_logo.png" alt="wmsu-logo" class="img-fluid">
        </p>
        <h1 class="fs-1 fw-bold my-3 mb-4 text-white text-center brand-color">MyWMSU</h1>

        <?php if (!empty($message)): ?>
          <div
            class="alert alert-<?= strpos($message, 'success') !== false ? 'success' : 'danger'; ?> alert-dismissible fade show"
            role="alert">
            <?= $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form action="#" method="post" onSubmit="return validate()">
          <div class="field-box">
            <div class="field">
              <label for="id">Employee ID</label> 
              <input type="text" id="emp_id" name="emp_id" placeholder="eg. 2019-1111" value="<?php if (isset($_POST['emp_id'])) {
                echo $_POST['emp_id'];
              } ?>">
              <?php
              $user_emp_id = new User();
              if (isset($_POST['emp_id'])) {
                $user_emp_id->emp_id = htmlentities($_POST['emp_id']);
              } else {
                $user_emp_id->emp_id = '';
              }

              if (isset($_POST['emp_id']) && strcmp(validate_emp_id($_POST['emp_id']), 'success') != 0) {
                ?>
                <p><?php echo validate_emp_id($_POST['emp_id']) ?></p>
                <?php
              } else if ($user_emp_id->is_emp_id_exist() && $_POST['emp_id']) {
                ?>
                  <p>Employee ID already exist</p>
                <?php
              }
              ?>
            </div>

            <div class="field">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" placeholder="eg. delacruz@wmsu.edu.ph" value="<?php if (isset($_POST['email'])) {
                echo $_POST['email'];
              } ?>">
              <?php
              $user_email = new User();
              if (isset($_POST['email'])) {
                $user_email->email = htmlentities($_POST['email']);
              } else {
                $user_email->email = '';
              }

              if (isset($_POST['email']) && strcmp(validate_email($_POST['email']), 'success') != 0) {
                ?>
                <?php
              } else if ($user_email->is_email_exist() && $_POST['email']) {
                ?>
                  <p>Email already exist</p>
                <?php
              }
              ?>
            </div>

            <div class="field">
              <label for="password">Password</label>
              <input type="password" id="password" name="password" value="<?php if (isset($_POST['password'])) {
                echo $_POST['password'];
              } ?>" onkeyup='check();'>
              <?php
              if (isset($_POST['password']) && validate_password($_POST['password']) !== "success") {
                ?>
                <div class="invalid-feedback pass-o">
                  <?= validate_password($_POST['password']) ?>
                </div>
                <?php
              }
              ?>
            </div>

            <div class="field">
              <label for="fname">First Name</label>
              <input type="text" name="f_name" id="f_name" value="<?php if (isset($_POST['f_name'])) {
                echo $_POST['f_name'];
              } ?>" required>
            </div>
            <div class="field">
              <label for="lname">Last Name</label>
              <input type="text" name="l_name" id="l_name" value="<?php if (isset($_POST['l_name'])) {
                echo $_POST['l_name'];
              } ?>" required>
            </div>
            <div class="field">
              <label for="mname">Middle Name</label>
              <input type="text" name="m_name" id="m_name" value="<?php if (isset($_POST['m_name'])) {
                echo $_POST['m_name'];
              } ?>">
            </div>
            <div class="field">
              <label for="faculty_type">Faculty Type</label>
              <select name="faculty_type" id="faculty_type">
                <option value="Regular Lecturer" <?php echo (isset($_POST['faculty_type']) && $_POST['faculty_type'] == 'Regular Lecturer') ? 'selected' : ''; ?>>Regular Lecturer</option>
                <option value="Visiting Lecturer" <?php echo (isset($_POST['faculty_type']) && $_POST['faculty_type'] == 'Visiting Lecturer') ? 'selected' : ''; ?>>Visiting Lecturer</option>
              </select>
            </div>
          </div>
          <button type="submit" class="btn d-flex p-3 justify-content-center align-items-center gap-2"
            <?= $signup_success ? 'disabled' : ''; ?>>
            <?= $signup_success ? 'Signing up <div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div>' : 'SIGN UP'; ?>
          </button>
          <div id="emailHelp" class="form-text d-flex justify-content-center">
            <p>Already have an account? <a href="login.php" style="color: #7797f7;"> Login</a></p>
          </div>
        </form>
      </div>
    </div>
  </main>

  <script>
    <?php if ($signup_success): ?>
      setTimeout(function () {
        window.location.href = './login.php';
      }, 2000);
    <?php endif; ?>
  </script>
</body>

</html>