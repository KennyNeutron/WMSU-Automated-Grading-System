<?php
session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1) {
  header('location: index');
} else if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 2) {
  header('location: ./admin/index');
}

require_once './tools/functions.php';
require_once './classes/signin.class.php';

if (isset($_POST['login'])) {
  $user = new Account();
  //sanitize
  $user->emp_id = htmlentities($_POST['emp_id']);
  $user->password = htmlentities($_POST['password']);

  if ($user->sign_in_user()) {
    $_SESSION['user_role'] = $user->user_role;
    $_SESSION['user_id'] = $user->id;
    $_SESSION['emp_id'] = $user->emp_id;
    $_SESSION['email'] = $user->email;
    $_SESSION['l_name'] = $user->l_name;
    $_SESSION['f_name'] = $user->f_name;
    $_SESSION['name'] = $user->name;
    $_SESSION['m_name'] = $user->m_name;
    $_SESSION['profile_image'] = $user->profile_image;
    $_SESSION['acad_rank'] = $user->acad_rank;

    if ($_SESSION['user_role'] == 1) {
      header('location: ./index.php');
    } else if ($_SESSION['user_role'] == 2) {
      header('location: ./admin/index.php');
    }
  } else {
    $error = 'Invalid ID/password';
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Login';
include_once './includes/head.php'
  ?>

<body class="login">
  <main>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
      <div class="login-page p-4">
        <p class="text-center">
          <img src="./img/wmsu_logo.png" alt="wmsu-logo" class="img-fluid">
        </p>
        <h1 class="fs-1 fw-bold my-3 mb-4 text-white text-center brand-color">MyWMSU</h1>
        <form action="#" method="post" id="loginForm" onSubmit="return validate()">
          <?php
          if (isset($_POST['login']) && isset($error)) {
            ?>
            <div class="alert alert-danger alert-dismissible fade show mb-5" role="alert">
              <?= $error ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
          }
          ?>
          <div class="field">
            <i class='bx bxs-user'></i>
            <input type="text" name="emp_id" id="emp_id" required value="<?php if (isset($_POST['emp_id'])) {
              echo $_POST['emp_id'];
            } ?>">
            <?php
            if (isset($_POST['emp_id']) && !validate_field($_POST['emp_id'])) {
              ?>
              <span>Enter your ID</span>
              <?php
            }
            ?>

            <label for="emp_id">School ID</label>
          </div>

          <div class="field">
            <i class='bx bxs-lock-alt'></i>
            <input type="password" name="password" id="password" required value="<?php if (isset($_POST['password'])) {
              echo $_POST['password'];
            } ?>">
            <?php
            if (isset($_POST['password']) && !validate_field($_POST['password'])) {
              ?>
              Enter your Password
              <?php
            }
            ?>
            <button type="button" class="toggle-password" style="background: none; color: white;"
              onclick="togglePasswordVisibility('password', this)">
              <i class="bx bx-show"></i>
            </button>
            <label for="password">Password</label>
          </div>

          <div class="w-full d-flex justify-content-end">
            <a href="./forgot-pass.php" id="forgot-pass" class="forgot-pass form-text w-auto" name="login"
              id="login">Forgot your password?</a>
          </div>
          <button type="submit" name="login" class="btn d-flex p-2 p-sm-3 justify-content-center">LOGIN</button>
          <div id="emailHelp" class="form-text d-flex justify-content-center">
            <p>Don't have an account?<a href="signup.php" style="color: #7797f7;"> Sign up</a></p>
          </div>
        </form>
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
  </script>
</body>

</html>