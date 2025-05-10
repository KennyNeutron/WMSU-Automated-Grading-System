<?php
session_start();

if (!isset($_SESSION['verification_id']) || empty($_SESSION['verification_id'])) {
  header('Location: ./forgot-pass.php');
  exit;
}

require_once './tools/functions.php';
require_once './classes/signin.class.php';

$error = "";
$success = false;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $submitted_code = implode('', $_POST['verification_code']);

  if ($submitted_code == $_SESSION['verification_code']) {
    $success = true;
  } else {
    $error = "Invalid verification code. Please try again.";
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Verify Email';
include_once './includes/head.php';
?>

<body class="login">
  <main>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
      <div class="login-page p-4">
        <p class="text-center">
          <img src="./img/wmsu_logo.png" alt="wmsu-logo" class="img-fluid">
        </p>
        <h1 class="fs-1 fw-bold my-3 mb-1 text-white text-center brand-color">Forgot Password</h1>
        <?php if ($error): ?>
          <div
            class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x w-auto mt-4 z-index-1050">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="alert alert-success d-flex flex-row align-items-center gap-2 position-fixed top-0 start-50 translate-middle-x w-auto mt-4 z-index-1050">
            Code verified successfully!
            <div class="spinner-border text-success" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        <?php endif; ?>
        <div class="row justify-content-center">
          <div class="col-12 col-md-6 col-lg-4" style="min-width: 350px;">
            <div class="card bg-white mb-5 mt-5 border-0" style="box-shadow: 0 12px 15px rgba(0, 0, 0, 0.02);">
              <div class="card-body px-3 py-5 text-center">
                <h4>Verify Email</h4>
                <p>Your verification code was sent to your email.</p>

                <form action="" method="post">
                  <div class="otp-field mb-5">
                    <input type="number" maxlength="1" name="verification_code[]" required />
                    <input type="number" maxlength="1" name="verification_code[]" required disabled />
                    <input type="number" maxlength="1" name="verification_code[]" required disabled />
                    <input type="number" maxlength="1" name="verification_code[]" required disabled />
                  </div>
                  <!-- <p class="resend text-muted">
                    Didn't receive code? <a href="" style="color: blue;">Request again</a>
                  </p> -->

                  <button type="submit" class="btn-primary py-2 px-4 rounded mb-3">
                    Verify
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
      const inputs = $(".otp-field input");
      const button = $(".btn");

      inputs.first().focus();

      button.prop("disabled", true);

      inputs.on("paste", function (e) {
        e.preventDefault();
        const pastedValue = e.originalEvent.clipboardData.getData("text");
        const otpLength = inputs.length;

        inputs.each(function (index) {
          if (index < pastedValue.length) {
            $(this).val(pastedValue[index]).prop("disabled", false);
          } else {
            $(this).val("").prop("disabled", true);
          }
        });

        inputs.each(function (index) {
          if (!$(this).prop("disabled")) {
            $(this).focus();
          }
        });
      });

      inputs.each(function (index) {
        $(this).on("keyup", function (e) {
          const currentInput = $(this);
          const nextInput = currentInput.next();
          const prevInput = currentInput.prev();

          if (currentInput.val().length > 1) {
            currentInput.val('');
            return;
          }

          if (nextInput.prop("disabled") && currentInput.val() !== "") {
            nextInput.prop("disabled", false).focus();
          }

          if (e.key === "Backspace" && prevInput.length) {
            currentInput.prop("disabled", true).val('');
            prevInput.focus();
          }

          button.prop("disabled", inputs.filter(function () {
            return $(this).prop("disabled") || $(this).val() === "";
          }).length > 0);
        });
      });
    });

    <?php if ($success): ?>
      setTimeout(function () {
        window.location.href = './reset-password.php?verification_id=<?= $_SESSION['verification_id']; ?>';
      }, 1500);
    <?php endif; ?>
  </script>
</body>

</html>