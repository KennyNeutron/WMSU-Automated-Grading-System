<?php
session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1) {
  header('location: index');
} else if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 2) {
  header('location: ./admin/index');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once './tools/functions.php';
require_once './classes/user.class.php';
require './vendor/PHPMailer-master/src/Exception.php';
require './vendor/PHPMailer-master/src/PHPMailer.php';
require './vendor/PHPMailer-master/src/SMTP.php';

$user = new User();
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $user->email = htmlentities($_POST['email']);

  if (!$user->is_email_exist()) {
    $error = "The email address doesn't exist.";
  } else {
    $verification_code = rand(1000, 9999);

    $_SESSION['verification_code'] = $verification_code;
    $_SESSION['verification_email'] = $user->email;
    $_SESSION['verification_id'] = $user->get_verification_id_by_email();

    $mail = new PHPMailer(true);

    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'techperficient.06@gmail.com';
      $mail->Password = 'unft zmfg crcz wgdm';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      $mail->setFrom('wmsu@gmail.com', 'MyWMSU');
      $mail->addAddress($user->email); 

      $mail->isHTML(true);
      $mail->Subject = 'Password Reset Verification Code';
      $mail->Body = "Hello,<br><br>We received a request to reset your password. Use the following code to verify your identity:<br><br><b>$verification_code</b><br><br>If you didn't request this, please ignore this email.";

      $mail->send();

      header("Location: ./verify-email.php?verification_id=" . $_SESSION['verification_id']);
      exit;

    } catch (Exception $e) {
      $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Forgot Password';
include_once './includes/head.php'
  ?>

<body class="login">
  <main>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
      <div class="login-page p-4">
        <p class="text-center">
          <img src="./img/wmsu_logo.png" alt="wmsu-logo" class="img-fluid">
        </p>
        <h1 class="fs-1 fw-bold my-3 mb-4 text-white text-center brand-color">Forgot Password?</h1>
        <form action="#" method="post" id="forgotForm" onSubmit="return validate()">
          <?php
          if (isset($error) && !empty($error)) {
            ?>
            <div class="alert alert-danger alert-dismissible fade show mb-5" role="alert">
              <?= $error ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
          }
          ?>
          <p style="color: white;">Request password reset</p>
          <div class="field">
            <i class='bx bxs-user'></i>
            <input type="email" name="email" id="email" required value="<?php if (isset($_POST['email'])) {
              echo $_POST['email'];
            } ?>">
            <?php
            if (isset($_POST['email']) && !validate_field($_POST['email'])) {
              ?>
              <span>Enter your Email</span>
              <?php
            }
            ?>

            <label for="email">Email</label>
          </div>

          <div class="d-flex flex-row gap-4">
            <a role="button" class="btn d-flex p-2 p-sm-3 justify-content-center" href="./login.php">Back</a>
            <button type="submit" name="next" class="btn d-flex p-2 p-sm-3 justify-content-center"
              style="background-color: #0260f7; color: white;">Next</button>
          </div>
        </form>
      </div>
    </div>
  </main>
</body>

</html>