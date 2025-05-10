<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login');
}

require_once '../tools/functions.php';
require_once '../classes/curr_year.class.php';
require_once '../classes/curri_page.class.php';
require_once '../classes/user.class.php';

$user = new User();
$curr_year = new Curr_year();
$subjects = new Curr_table();

$success = false;
$errors = '';
$message = '';
$currentYear = date('Y');

if (isset($_POST['add_curr-year'])) {
  $record = $user->fetch($_SESSION['user_id']);
  $user->user_id = $_SESSION['user_id'];

  $curr_year->user_id = $_SESSION['user_id'];
  $curr_year->year_start = htmlentities($_POST['year_start']);
  $curr_year->year_end = htmlentities($_POST['year_end']);

  if (!validate_field($curr_year->year_start)) {
    $errors = 'Please enter Curriculum Year Start.';
  } elseif ($curr_year->is_year_exist($curr_year->year_start)) {
    $errors = 'Curriculum Year Start already exists.';
  } elseif ($curr_year->year_start < $currentYear) {
    $errors = 'Please enter the current year or a future year.';
  } elseif (!validate_field($curr_year->year_end)) {
    $errors = 'Please enter Curriculum Year End.';
  } elseif (
    validate_field($curr_year->year_start) && !$curr_year->is_year_exist($curr_year->year_start) &&
    validate_field($curr_year->year_end)
  ) {
    if ($newData = $curr_year->add()) {
      // Copy existing data from the previous curriculum year to the new curriculum year
      $previous = $curr_year->year_start - 1;
      $previous_id = $curr_year->fetchByYearStart($previous);
      $new_data = $curr_year->year_start;
      $new_id = $curr_year->fetchByYearStart($new_data);
      if ($previous_id && $new_id) {
        $subjects->copyCurrTableData($previous_id['curr_year_id'], $new_id['curr_year_id']);
      }
      $message = 'Curriculum Year is successfully added.';
      $success = true;
    } else {
      $message = 'Something went wrong adding Curriculum Year.';
    }
  }
}

$currentYear = date('Y');

?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Add Curriculum';
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
            <span class="fs-2 fw-bold h1 m-0 brand-color">Add Curriculum</span>
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
                <input type="number" class="form-control" placeholder="YYYY" min="1999" id="year_start"
                  name="year_start" value="<?= htmlspecialchars($curr_year->year_start ?? '') ?>">
              </div>

              <div class="mb-3">
                <label for="year_end" class="form-label">Curriculum Year End</label>
                <input type="number" class="form-control" placeholder="YYYY" id="year_end" name="year_end"
                  value="<?= htmlspecialchars($curr_year->year_end ?? '') ?>" readonly>
              </div>
            </div>
          </div>
          <button type="button" onclick="history.back()" class="btn btn-secondary">Cancel</button>
          <button type="submit" class="btn brand-bg-color" id="add_curr-year" name="add_curr-year">Add</button>
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
          $('#year_end').val(startYear + 1);
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