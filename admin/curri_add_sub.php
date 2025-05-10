<?php
require_once '../tools/functions.php';
require_once '../classes/curri_page.class.php';
require_once '../classes/user.class.php';
require_once '../classes/curr_year.class.php';

session_start();

// Check if the user is authorized
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
  header('Location: ../login');
  exit();
}

$error_message = '';
$success = false;

if (isset($_POST['add_curr_sub'])) {
  $user = new User();
  $record = $user->fetch($_SESSION['user_id']);
  $user->user_id = $_SESSION['user_id'];

  $curr_table = new Curr_table();

  // Sanitize inputs
  $curr_table->user_id = $_SESSION['user_id'];
  $curr_table->curr_year_id = htmlentities($_GET['year_id']);
  $curr_table->college_course_id = htmlentities($_GET['course_id']);
  $curr_table->time_id = htmlentities($_GET['time_id']);
  $curr_table->year_level_id = htmlentities($_GET['year_level_id']);
  $curr_table->semester_id = htmlentities($_GET['semester_id']);
  $curr_table->sub_code = htmlentities($_POST['sub_code']);
  $curr_table->sub_name = htmlentities($_POST['sub_name']);
  $selected_prerequisite = isset($_POST['sub_prerequisite']) ? $_POST['sub_prerequisite'] : [];
  $curr_table->lec = htmlentities($_POST['lec']) ?? 0;
  $curr_table->lab = htmlentities($_POST['lab']) ?? 0;

  if (
    validate_field($curr_table->sub_code) &&
    !$curr_table->is_subcode_exist($curr_table->sub_code) &&
    validate_field($curr_table->sub_name)
  ) {
    $sub_prerequisites = implode(',', $selected_prerequisite);
    if ($curr_table->add($sub_prerequisites)) {
      $redirect_url = './curri_page.php?year_id=' . urlencode($_GET['year_id']) .
        '&course_id=' . urlencode($_GET['course_id']) .
        '&time_id=' . urlencode($_GET['time_id']) .
        '&year_level_id=' . urlencode($_GET['year_level_id']) .
        '&semester_id=' . urlencode($_GET['semester_id']);
      $success = true;
      $message = 'Subject added';
    } else {
      $error_message = 'Something went wrong while adding the subject.';
    }
  } else {
    $error_message = 'Validation failed or subject code already exists.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Add Subject';
$curriculum_page = 'active';
include '../includes/admin_head.php';
?>

<body>
  <div class="home">
    <div class="side">
      <?php include '../includes/admin_sidepanel.php'; ?>
    </div>
    <main>
      <div class="header">
        <?php include '../includes/admin_header.php'; ?>
      </div>

      <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
        <div class="d-flex align-items-center">
          <a href="./curri_page.php?year_id=<?= $_GET['year_id'] ?>&course_id=<?= $_GET['course_id'] ?>&time_id=<?= $_GET['time_id'] ?>&year_level_id=<?= $_GET['year_level_id'] ?>&semester_id=<?= $_GET['semester_id'] ?>" class="bg-none d-flex align-items-center">
            <i class='bx bx-chevron-left fs-2 brand-color'></i>
          </a>
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">Add Subject</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <form action="" method="POST">
          <!-- Error Messages -->
          <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($error_message) ?>
            </div>
          <?php endif; ?>

          <!-- Success Messages -->
          <?php if ($success): ?>
            <div class="alert alert-success gap-2">
              <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?> successfully!
            </div>
          <?php endif; ?>

          <div class="row row-cols-1 row-cols-md-2 align-items-start">
            <div class="col">
              <!-- Subject Code -->
              <div class="mb-3">
                <label for="sub_code" class="form-label">Subject Code</label>
                <input type="text" class="form-control" id="sub_code" placeholder="eg. ETHICS 101" name="sub_code" 
                  value="<?= isset($_POST['sub_code']) ? htmlspecialchars($_POST['sub_code']) : '' ?>">
                <?php if (isset($_POST['sub_code']) && !$curr_table->is_subcode_exist($_POST['sub_code'])): ?>
                  <p class="text-danger my-1">Subject code already exists.</p>
                <?php endif; ?>
              </div>

              <!-- Subject Name -->
              <div class="mb-3">
                <label for="sub_name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="sub_name" placeholder="eg. Ethics 1" name="sub_name"
                  value="<?= isset($_POST['sub_name']) ? htmlspecialchars($_POST['sub_name']) : '' ?>">
              </div>

              <!-- Select Prerequisite -->
              <?php
              $curr_table = new Curr_table();
              $existing_sub = $curr_table->fetchSubDetailsByYearAndCourseId($_GET['course_id']);
              ?>
              <div class="mb-3">
                <label for="sub_prerequisite" class="form-label">Select Prerequisite</label>
                <select class="form-select" id="sub_prerequisite" name="sub_prerequisite[]" multiple>
                  <?php foreach ($existing_sub as $sub): ?>
                    <option value="<?= $sub['sub_code'] ?>" <?= isset($_POST['sub_prerequisite']) && in_array($sub['sub_code'], $_POST['sub_prerequisite']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($sub['sub_code']) ?> - <?= htmlspecialchars($sub['sub_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col">
              <!-- Lecture -->
              <div class="mb-3">
                <label for="lec" class="form-label">Lecture</label>
                <input type="number" class="form-control" id="lec" name="lec"
                  value="<?= isset($_POST['lec']) ? htmlspecialchars($_POST['lec']) : 0 ?>">
              </div>

              <!-- Laboratory -->
              <div class="mb-3">
                <label for="lab" class="form-label">Laboratory</label>
                <input type="number" class="form-control" id="lab" name="lab"
                  value="<?= isset($_POST['lab']) ? htmlspecialchars($_POST['lab']) : 0 ?>">
              </div>

              <!-- Total Unit -->
              <div class="mb-3">
                <label for="total_unit" class="form-label">Total Unit</label>
                <input type="number" class="form-control" id="total_unit" disabled
                  value="<?= isset($_POST['lec']) && isset($_POST['lab']) ? (int) $_POST['lec'] + (int) $_POST['lab'] : 0 ?>">
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <button type="button" onclick="cancelAdd();" class="btn btn-secondary">Cancel</button>
            <button type="submit" id="add_curr_sub" name="add_curr_sub" class="btn brand-bg-color">Submit</button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script src="./js/main.js"></script>
  <script>
    new MultiSelectTag('sub_prerequisite', {
      rounded: true,    // default true
      shadow: true,      // default false
      placeholder: 'Search Subject',  // default Search...
      tagColor: {
        textColor: '#952323',
        borderColor: '#dd3838',
        bgColor: '#e59d9d',
      },
      onChange: function (values) {
        console.log(values)
      }
    })

    $(document).ready(function () {
      $('#lec, #lab').on('input', function () {
        var lec = parseInt($('#lec').val()) || 0;
        var lab = parseInt($('#lab').val()) || 0;
        var totalUnit = lec + lab;
        $('#total_unit').val(totalUnit);
      });
    });

    function cancelAdd() {
      var yearId = encodeURIComponent('<?php echo $_GET['year_id']; ?>');
      var courseId = encodeURIComponent('<?php echo $_GET['course_id']; ?>');
      var timeId = encodeURIComponent('<?php echo $_GET['time_id']; ?>');
      var yearLevelId = encodeURIComponent('<?php echo $_GET['year_level_id']; ?>');
      var semesterId = encodeURIComponent('<?php echo $_GET['semester_id']; ?>');

      var redirectUrl = './curri_page.php?year_id=' + yearId + '&course_id=' + courseId + '&time_id=' + timeId + '&year_level_id=' + yearLevelId + '&semester_id=' + semesterId;

      window.location.href = redirectUrl;
    }

    <?php if ($success): ?>
      setTimeout(function () {

        var yearId = encodeURIComponent('<?php echo $_GET['year_id']; ?>');
        var courseId = encodeURIComponent('<?php echo $_GET['course_id']; ?>');
        var timeId = encodeURIComponent('<?php echo $_GET['time_id']; ?>');
        var yearLevelId = encodeURIComponent('<?php echo $_GET['year_level_id']; ?>');
        var semesterId = encodeURIComponent('<?php echo $_GET['semester_id']; ?>');

        var redirectUrl = './curri_page.php?year_id=' + yearId + '&course_id=' + courseId + '&time_id=' + timeId + '&year_level_id=' + yearLevelId + '&semester_id=' + semesterId;

        window.location.href = redirectUrl;
      }, 1500);
    <?php endif; ?>
  </script>

</body>

</html>