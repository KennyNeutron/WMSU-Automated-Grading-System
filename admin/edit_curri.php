<?php

require_once '../tools/functions.php';
require_once '../classes/curr_year.class.php';
require_once '../classes/user.class.php';

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login');
}

$errors = "";
$message = '';
$success = false;
$curr_year = new Curr_year();

if (isset($_GET['curr_year_id'])) {
  $record = $curr_year->fetch($_GET['curr_year_id']);

  $curr_year->curr_year_id = $record['curr_year_id'];
  $curr_year->year_start = $record['year_start'];
  $curr_year->year_end = $record['year_end'];
}


if (isset($_POST['edit_curr_year'])) {
  $curr_year->curr_year_id = $_GET['curr_year_id'];
  $curr_year->year_start = intval($_POST['year_start']);
  $curr_year->year_end = intval($_POST['year_end']);

  if (!validate_field($curr_year->year_start)) {
    $errors = 'Please enter Curriculum Year Start.';
  }

  if ($curr_year->is_year_exist($curr_year->year_start)) {
    $errors = 'Curriculum Year Start already exists.';
  }

  if (!validate_field($curr_year->year_end)) {
    $errors = 'Please enter Curriculum Year End.';
  } elseif ($curr_year->year_end !== $curr_year->year_start + 1) {
    $errors = 'Year End must be equal to Year Start + 1.';
  }

  if (empty($errors)) {
    if ($curr_year->edit()) {
      $message = 'Curriculum updated successfully.';
      $success = true;
    } else {
      $message = 'Something went wrong while editing the curriculum.';
    }
  }
}

$currentYear = date('Y');
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Edit Curriculum';
$curriculum_page = 'active';
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
          <button onclick="history.back()" class="bg-none"><i class='bx bx-chevron-left fs-2 brand-color'></i></button>
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">Edit Curriculum</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <form action="#" method="post">
          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($errors) ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($message)): ?>
            <div class="alert alert-success gap-2">
            <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?>
            </div>
          <?php endif; ?>

          <div class="row row-cols-1 row-cols-md-2">
            <div class="col">
              <div class="mb-3">
                <label for="year_start" class="form-label">Curriculum Year Start</label>
                <input type="number" class="form-control" placeholder="YYYY" min="1999" 
                  id="year_start" name="year_start" value="<?= htmlspecialchars($curr_year->year_start ?? '') ?>">
              </div>

              <div class="mb-3">
                <label for="year_end" class="form-label">Curriculum Year End</label>
                <input type="number" class="form-control" placeholder="YYYY" id="year_end" name="year_end"
                  value="<?= htmlspecialchars($curr_year->year_end ?? '') ?>" readonly>
              </div>
            </div>
          </div>

          <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
          <button type="submit" class="btn brand-bg-color" name="edit_curr_year"><i
          class='bx bxs-save me-2'></i>Save Changes</button>
        </form>

      </div>

    </main>
  </div>

  <script src="./js/main.js"></script>

  <script>
    $(document).ready(function () {
      $('#year_start').on('input', function () {
        var startYear = parseInt($(this).val());
        if (!isNaN(startYear)) {
          $('#year_end').attr('value', startYear + 1);
        }
      });
    });

    <?php if ($success): ?>
      setTimeout(function () {
        window.location.href = './index.php';
      }, 1500);
    <?php endif; ?>
  </script>

</body>

</html>