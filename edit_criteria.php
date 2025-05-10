<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login.php');
  exit();
}

require_once './classes/period.class.php';
require_once './classes/component.class.php';

$period = new Periods();
$components = new SubjectComponents();

$error_message = '';
$success = false;
$totalWeight = 0;

$criteria_id = isset($_GET['component_id']) ? $_GET['component_id'] : null;
$active_period = isset($_GET['period']) ? $_GET['period'] : null;
$selected_faculty_sub_id = isset($_GET['faculty_sub_id']) ? $_GET['faculty_sub_id'] : null;
$criteria_data = $components->getComponentById($criteria_id);
$gradingComponents = ($active_period === 'finalterm') ? $period->showFinalterm($selected_faculty_sub_id) : $period->showMidterm($selected_faculty_sub_id);

foreach($gradingComponents as $item) {
  $totalWeight += $item['weight'];
}

$totalWeight -= $criteria_data['weight'];

if (isset($_POST['edit_criteria'])) {
  $component_type = ucwords($_POST['component_type']);
  $weight = floatval(htmlentities($_POST['weight']));

  $newTotalWeight = $totalWeight + $weight;

  if ($newTotalWeight > 100) {
    $error_message = 'Total weight exceeds 100%. Please adjust the criteria weight.';
  } else {
    $components->component_id = $criteria_id;
    $components->component_type = $component_type;
    $components->weight = $weight;

    if ($components->edit()) {
      $message = 'Criteria updated successfully!';
      $success = true;
    } else {
      $error_message = 'Something went wrong while updating criteria.';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Edit Criteria';
$sub_setting_page = 'active';
include './includes/head.php';
?>

<body>
  <div class="home">
    <div class="side">
      <?php require_once('./includes/sidepanel.php'); ?>
    </div>
    <main>
      <div class="header">
        <?php require_once('./includes/header.php'); ?>
      </div>

      <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
        <div class="d-flex align-items-center">
          <button onclick="history.back()" class="bg-none"><i class='bx bx-chevron-left fs-2 brand-color'></i></button>
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 h1 m-0">Edit Criteria</span>
          </div>
        </div>
      </div>

      <div class="m-5 py-3">
        <form action="#" method="post">

          <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($error_message) ?>
            </div>
          <?php endif; ?>

          <?php if ($success): ?>
            <div class="alert alert-success gap-2">
              <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?>
            </div>
          <?php endif; ?>

          <div class="row row-cols-1 row-cols-md-2">
            <div class="col">
              <div class="mb-3">
                <label for="component_type" class="form-label">Criteria Name</label>
                <input type="text" class="form-control" name="component_type" id="component_type"
                  placeholder="eg. Activities"
                  value="<?= htmlspecialchars($_POST['component_type'] ?? $criteria_data['component_type']) ?>">
              </div>
              <div class="mb-3">
                <label for="weight" class="form-label">Weight</label>
                <div class="input-group" style="width: 150px;">
                  <input type="number" class="form-control" name="weight" id="weight" value="<?= htmlspecialchars($_POST['weight'] ?? $criteria_data['weight']) ?>">
                  <span class="input-group-text">%</span>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-start mt-4 gap-2">
            <button onclick="history.back()" type="button" class="btn btn-secondary">Cancel</button>
            <button type="submit" name="edit_criteria" class="btn brand-bg-color"><i class='bx bxs-save me-2'></i>Save
              Changes</button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script src="./js/main.js"></script>
  <script>
    <?php if ($success): ?>
      setTimeout(function () {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = './subject_setting.php';

        const facultyInput = document.createElement('input');
        facultyInput.type = 'hidden';
        facultyInput.name = 'faculty_sub_id';
        facultyInput.value = "<?= htmlspecialchars($selected_faculty_sub_id) ?>";
        form.appendChild(facultyInput);

        document.body.appendChild(form);
        form.submit();
      }, 1500);
    <?php endif; ?>
  </script>
</body>
</html>
