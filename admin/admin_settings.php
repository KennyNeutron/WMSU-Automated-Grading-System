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
$user->f_name = $record['f_name'];
$user->l_name = $record['l_name'];
$user->m_name = $record['m_name'];
$user->user_id = $user_id;

if (isset($_POST['save_settings'])) {
  try {
    $user->f_name = htmlentities($_POST['f_name']);
    $user->l_name = htmlentities($_POST['l_name']);
    $user->m_name = htmlentities($_POST['m_name']);

    $errors = [];
    if (!validate_field($user->f_name)) {
      $errors[] = 'Please enter First Name';
    }
    if (!validate_field($user->l_name)) {
      $errors[] = 'Please enter Last Name';
    }

    if (empty($errors)) {
      if ($user->edit()) {
        $_SESSION['f_name'] = $user->f_name;
        $_SESSION['l_name'] = $user->l_name;
        $_SESSION['m_name'] = $user->m_name;

        $success = true;
        $message = 'User Info Changed';
      } else {
        $message = 'Something went wrong updating user details.';
      }
    } else {
      throw new Exception(implode('<br>', $errors));
    }
  } catch (Exception $e) {
    $error_message = $e->getMessage();
  }
}

if (isset($_POST['saveimage'])) {
  $uploaddir = '../img/profile-img/';
  $fileName = 'admin-profile' . '.' . pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION); // Set the new name
  $uploadfile = $uploaddir . $fileName;

  if (isset($_SESSION['profile_image']) && $_SESSION['profile_image'] != 'admin-profile') {
    $existingImage = $uploaddir . $_SESSION['profile_image'];
    if (file_exists($existingImage)) {
      unlink($existingImage);
    }
  }

  if (move_uploaded_file($_FILES['profile']['tmp_name'], $uploadfile)) {
    $_SESSION['profile_image'] = $fileName;
    $message = 'Profile image successfully updated.';
  } else {
    $message = 'Failed to upload image.';
  }

  $user->profile_image = $fileName;

  if (validate_field($user->profile_image)) {
    if ($user->edit_profile($user_id)) {
      $_SESSION['profile_image'] = $user->profile_image;
      $success = true;
      $message = 'Profile image changed';
    } else {
      $message = 'Something went wrong while updating the image.';
    }
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
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">Settings</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="container-fluid d-flex justify-content-start">
            <span class="fs-2 fw-bold h1 m-0 brand-color mb-3">User Settings</span>
          </div>
          <div class="row row-cols-1 row-cols-md-2">
            <div class="col-md-4">
              <div class="border shadow p-3 mb-5 bg-body rounded">
                <div class="user">
                  <div class="name d-flex justify-content-center align-items-center">
                    <p class="username fw-bold"><?= $_SESSION['l_name'] ?> - <?= $_SESSION['f_name'] ?></p>
                  </div>
                  <div class="profile-pic">
                    <label class="label brand-border-color d-flex flex-column rounded-circle" for="file"
                      style="border-width: 4px !important;">
                      <i class="bx bxs-camera-plus"></i>
                      <span>Change Image</span>
                    </label>
                    <img src="../img/profile-img/<?= $_SESSION['profile_image'] ?>" id="output"
                      class="img-fluid rounded-circle">
                    <input id="file" type="file" name="profile" accept="image/png, image/jpeg"
                      onchange="validateFile(event)">
                  </div>
                  <div class="d-flex justify-content-center align-items-center mb-2">
                    <button type="submit" name="saveimage" class="btn brand-bg-color" id="saveImageButton" disabled>Save
                      Profile Image</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-8">
              <div class="row row-cols-1 row-cols-md-2">
                <div class="col">
                  <div class="mb-3">
                    <label for="f_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="f_name" name="f_name" aria-describedby="f_name" value="<?php if (isset($_POST['f_name'])) {
                      echo $_POST['f_name'];
                    } else if (isset($user->f_name)) {
                      echo $user->f_name;
                    } ?>" oninput="capitalizeFirstLetter(this)">
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label for="l_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="l_name" name="l_name" aria-describedby="l_name" value="<?php if (isset($_POST['l_name'])) {
                      echo $_POST['l_name'];
                    } else if (isset($user->l_name)) {
                      echo $user->l_name;
                    } ?>" oninput="capitalizeFirstLetter(this)">
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label for="m_name" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="m_name" name="m_name" aria-describedby="m_name" value="<?php if (isset($_POST['m_name'])) {
                      echo $_POST['m_name'];
                    } else if (isset($user->m_name)) {
                      echo $user->m_name;
                    } ?>" oninput="capitalizeFirstLetter(this)">
                  </div>
                </div>
              </div>

              <div class="mb-2">
                <a class="btn btn-toggle link-dark d-flex align-items-center nav-link p-0" href="./manage_password.php" role="button">
                  <i class='bx bxs-key me-2'></i>
                  <span class="change-pass">Change password</span>
                </a>
              </div>

            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="submit" name="save_settings" class="btn brand-bg-color py-2 px-3 rounded"
              id="changeSettingsButton" disabled>Change</button>
          </div>
        </form>

      </div>

    </main>
  </div>

  <script src="./js/main.js"></script>
  <script>
    var validateFile = function (event) {
      var fileInput = event.target;
      var filePath = fileInput.value;
      var allowedExtensions = /(\.png|\.jpeg|\.jpg)$/i;

      if (!allowedExtensions.exec(filePath)) {
        alert('Invalid file type. Only PNG and JPEG files are allowed.');
        fileInput.value = '';
        return false;
      }

      var image = document.getElementById("output");
      image.src = URL.createObjectURL(event.target.files[0]);
    };

    function capitalizeFirstLetter(input) {
      input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
    }

    $(document).ready(function () {
      $('#saveImage').click(function () {
        var formData = new FormData($('#profileForm')[0]);

        $.ajax({
          url: '../includes/upload_profile.php',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: function (response) {
            alert(response);
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
            alert('Error occurred while uploading image: ' + error);
          }
        });
      });
    });

    $(document).ready(function () {
      $('#save_settings').click(function (e) {
        e.preventDefault();

        var formData = $('#settings_form').serialize();

        $.ajax({
          url: 'update_settings',
          type: 'POST',
          data: formData,
          success: function (response) {
            alert('Settings updated successfully!');
            location.reload();
          },
          error: function (xhr) {
            const errorMessage = xhr.responseText || "An unexpected error occurred.";
            alert('Error: ' + errorMessage);
          }
        });
      });
    });

    var validateFile = function (event) {
      var fileInput = event.target;
      var filePath = fileInput.value;
      var allowedExtensions = /(\.png|\.jpeg|\.jpg)$/i;

      if (!allowedExtensions.exec(filePath)) {
        alert('Invalid file type. Only PNG and JPEG files are allowed.');
        fileInput.value = '';
        document.getElementById("saveImageButton").disabled = true;
        return false;
      }

      var image = document.getElementById("output");
      image.src = URL.createObjectURL(event.target.files[0]);
      document.getElementById("saveImageButton").disabled = false;
    };

    document.getElementById("file").addEventListener("change", function () {
      const saveButton = document.getElementById("saveImageButton");
      saveButton.disabled = !this.value;
    });

    document.getElementById("file").addEventListener("change", function () {
      const saveImageButton = document.getElementById("saveImageButton");
      saveImageButton.disabled = !this.value;
    });

    const generalInputs = document.querySelectorAll("#f_name, #l_name, #m_name");
    const changeSettingsButton = document.getElementById("changeSettingsButton");
    generalInputs.forEach((input) => {
      input.addEventListener("input", function () {
        changeSettingsButton.disabled = !Array.from(generalInputs).some((field) => field.value.trim() !== "");
      });
    });
  </script>

</body>

</html>