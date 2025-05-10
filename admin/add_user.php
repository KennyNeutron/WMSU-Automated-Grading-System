<?php
require_once '../tools/functions.php';
require_once '../classes/profiling.class.php';

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login');
}

$error_message = '';
$success = false;

if (isset($_POST['add_faculty'])) {
  $profiling = new Profiling();
  //sanitize
  $profiling->emp_id = htmlentities($_POST['emp_id']);
  $profiling->f_name = htmlentities($_POST['f_name']);
  $profiling->l_name = htmlentities($_POST['l_name']);
  $profiling->m_name = htmlentities($_POST['m_name']);
  $profiling->email = htmlentities($_POST['email']);
  $profiling->start_service = htmlentities($_POST['start_service']);
  $profiling->acad_type = htmlentities($_POST['acad_type']);
  $profiling->faculty_type = htmlentities($_POST['faculty_type']);
  $profiling->designation = htmlentities($_POST['designation']);
  $profiling->department_id = htmlentities($_POST['department_id']);

  if (
    validate_field($profiling->emp_id) && !$profiling->is_empId_exist($profiling->emp_id) &&
    validate_field($profiling->f_name) &&
    validate_field($profiling->l_name) &&
    validate_field($profiling->email) && !$profiling->is_email_exist($profiling->email) &&
    validate_field($profiling->start_service) &&
    validate_field($profiling->acad_type) &&
    validate_field($profiling->faculty_type) &&
    validate_field($profiling->designation)
  ) {
    // If "End of Service" is not set, set it to NULL
    $profiling->end_service = isset($_POST['end_service']) ? htmlentities($_POST['end_service']) : null;

    if ($profiling->add()) {
      header('location: ./profiling.php');
      $message = 'Faculty added';
      $success = true;
    } else {
      $error_message = 'Something went wrong adding Faculty.';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Grade Posted';
$manage_acc = 'active';
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
          <button onclick="history.back()" class="bg-none d-flex align-items-center"><i
              class='bx bx-chevron-left fs-2 brand-color'></i></button>
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">Add User</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <form action="#" method="post">
          <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($error_message) ?>
            </div>
          <?php endif; ?>

          <?php if ($success): ?>
            <div class="alert alert-success gap-2">
              <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?> successfully!
            </div>
          <?php endif; ?>

          <div class="row row-cols-1 row-cols-md-2">
            <div class="col">
              <div class="mb-3">
                <label for="emp_id" class="form-label">Employee ID</label>
                <input type="text" class="form-control" id="emp_id" name="emp_id" aria-describedby="emp_id" value="<?php if (isset($_POST['emp_id'])) {
                  echo $_POST['emp_id'];
                } ?>">

                <?php
                if (isset($_POST['emp_id']) && !validate_field($_POST['emp_id'])) {
                  ?>
                  <p class="text-danger my-1">Please enter Employee ID</p>
                  <?php
                }
                ?>
                <?php
                if (isset($_POST['emp_id']) && $profiling->is_empId_exist($_POST['emp_id'])) {
                  ?>
                  <p>Employee ID already exists</p>
                  <?php
                }
                ?>
              </div>
              <div class="mb-3">
                <label for="f_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="f_name" name="f_name" aria-describedby="f_name" value="<?php if (isset($_POST['f_name'])) {
                  echo $_POST['f_name'];
                } ?>" oninput="capitalizeFirstLetter(this)">
                <?php
                if (isset($_POST['f_name']) && !validate_field($_POST['f_name'])) {
                  ?>
                  <p class="text-danger my-1">Please enter first name</p>
                  <?php
                }
                ?>
              </div>

              <div class="mb-3">
                <label for="l_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="l_name" name="l_name" aria-describedby="l_name" value="<?php if (isset($_POST['l_name'])) {
                  echo $_POST['l_name'];
                } ?>" oninput="capitalizeFirstLetter(this)">
                <?php
                if (isset($_POST['l_name']) && !validate_field($_POST['l_name'])) {
                  ?>
                  <p class="text-danger my-1">Please enter last name</p>
                  <?php
                }
                ?>
              </div>

              <div class="mb-3">
                <label for="m_name" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="m_name" name="m_name" aria-describedby="m_name" value="<?php if (isset($_POST['m_name'])) {
                  echo $_POST['m_name'];
                } ?>" oninput="capitalizeFirstLetter(this)">
                <?php
                if (isset($_POST['m_name']) && !validate_field($_POST['m_name'])) {
                  ?>
                  <p class="text-danger my-1">Please enter middle name</p>
                  <?php
                }
                ?>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="email" value="<?php if (isset($_POST['email'])) {
                  echo $_POST['email'];
                } ?>">
                <?php
                if (isset($_POST['email']) && !validate_field($_POST['email'])) {
                  ?>
                  <p class="text-danger my-1">Please enter email address</p>
                  <?php
                }
                ?>
                <?php
                if (isset($_POST['email']) && $profiling->is_email_exist($_POST['email'])) {
                  ?>
                  <p> email address already exists</p>
                  <?php
                }
                ?>
              </div>
            </div>

            <div class="col">
              <div class="row row-cols-md-2">

                <div class="col">
                  <div class="mb-3">
                    <label for="start_service" class="form-label">Start Service</label>
                    <input type="date" class="form-control" id="start_service" name="start_service"
                      aria-describedby="start_service" value="<?php if (isset($_POST['start_service'])) {
                        echo $_POST['start_service'];
                      } ?>">
                    <?php
                    if (isset($_POST['start_service']) && !validate_field($_POST['start_service'])) {
                      ?>
                      <p class="text-danger my-1">Please enter start</p>
                      <?php
                    }
                    ?>
                  </div>
                </div>

                <div class="col">
                  <div class="mb-3">
                    <label for="end_service" class="form-label">End Service</label>
                    <input type="date" class="form-control" id="end_service" name="end_service"
                      aria-describedby="end_service" value="<?php if (isset($_POST['end_service'])) {
                        echo $_POST['end_service'];
                      } ?>">
                  </div>
                </div>
              </div>
              <div class="mb-3" <?= isset($_GET['department_id']) ? 'style="display: none;"' : '' ?>>
                <?php
                require_once '../classes/department.class.php';
                require_once '../tools/functions.php';

                $department = new Department();

                $department_array = $department->show();

                $show_collapse = false;
                if (isset($_GET['department_id'])) {
                  $show_collapse = true;
                }
                ?>
                <label for="department_id" class="form-label">Department</label>
                <select class="dropdown-toggle form-select" id="department_id" name="department_id">
                  <?php
                  if ($department_array) {
                    foreach ($department_array as $item) {
                      ?>
                      <option value="<?= $item['department_id'] ?>" <?php if (isset($_GET['department_id']) && $_GET['department_id'] == $item['department_id']) {
                          echo 'selected';
                        } ?>>
                        <?php echo $item['department_name'] ?>
                      </option>
                      <?php
                    }
                  }
                  ?>
                </select>
              </div>


              <div class="mb-3">
                <label for="acad_type" class="form-label">Academic Rank</label>
                <select type="button" class="dropdown-toggle form-select" data-bs-toggle="dropdown" id="acad_type"
                  name="acad_type">
                  <!-- <option value="">Select Rank</option> -->
                  <option value="Adjunct Faculty" <?php if (isset($_POST['acad_type']) && $_POST['acad_type'] == 'Adjunct Faculty') {
                    echo 'selected';
                  } ?>>Adjunct Faculty</option>
                  <option value="Instructor" <?php if (isset($_POST['acad_type']) && $_POST['acad_type'] == 'Instructor') {
                    echo 'selected';
                  } ?>>Instructor</option>
                  <option value="Instructor II" <?php if (isset($_POST['acad_type']) && $_POST['acad_type'] == 'Instructor II') {
                    echo 'selected';
                  } ?>>Instructor II</option>
                  <option value="Instructor III" <?php if (isset($_POST['acad_type']) && $_POST['acad_type'] == 'Instructor III') {
                    echo 'selected';
                  } ?>>Instructor III</option>
                  <option value="Professor I" <?php if (isset($_POST['acad_type']) && $_POST['acad_type'] == 'Professor I') {
                    echo 'selected';
                  } ?>>Professor I</option>
                  <option value="Professor II" <?php if (isset($_POST['acad_type']) && $_POST['acad_type'] == 'Professor II') {
                    echo 'selected';
                  } ?>>Professor II</option>
                  <option value="Professor III" <?php if (isset($_POST['acad_type']) && $_POST['acad_type'] == 'Professor III') {
                    echo 'selected';
                  } ?>>Professor III</option>
                </select>
                <?php
                if (isset($_POST['acad_type']) && !validate_field($_POST['acad_type'])) {
                  ?>
                  <p class="text-danger my-1">Select Academic Rank!</p>
                  <?php
                }
                ?>
              </div>
              <div class="mb-3">
                <label for="faculty_type" class="form-label">Faculty Type</label>
                <select type="button" class="dropdown-toggle form-select" data-bs-toggle="dropdown" id="faculty_type"
                  name="faculty_type">
                  <!-- <option value="">Select Type</option> -->
                  <option value="Regular Lecturer" <?php if (isset($_POST['faculty_type']) && $_POST['faculty_type'] == 'Regular Lecturer') {
                    echo 'selected';
                  } ?>>Regular Lecturer</option>
                  <option value="Visiting Lecturer" <?php if (isset($_POST['faculty_type']) && $_POST['faculty_type'] == 'Visiting Lecturer') {
                    echo 'selected';
                  } ?>>Visiting Lecturer</option>
                </select>
                <?php
                if (isset($_POST['faculty_type']) && !validate_field($_POST['faculty_type'])) {
                  ?>
                  <p class="text-danger my-1">Select Faculty Type!</p>
                  <?php
                }
                ?>
              </div>
              <div class="mb-3">
                <label for="designation" class="form-label">Designation</label>
                <select type="button" class="dropdown-toggle form-select" data-bs-toggle="dropdown" id="designation"
                  name="designation">
                  <!-- <option value="">Select Designation</option> -->
                  <option value="Professor" <?php if (isset($_POST['designation']) && $_POST['designation'] == 'Professor') {
                    echo 'selected';
                  } ?>>Professor</option>
                  <option value="Assistant Professor" <?php if (isset($_POST['designation']) && $_POST['designation'] == 'Assistant Professor') {
                    echo 'selected';
                  } ?>>Assistant Professor</option>
                  <option value="Academic Staff" <?php if (isset($_POST['designation']) && $_POST['designation'] == 'Academic Staff') {
                    echo 'selected';
                  } ?>>Academic Staff</option>
                  <option value="Associate Professor" <?php if (isset($_POST['designation']) && $_POST['designation'] == 'Associate Professor') {
                    echo 'selected';
                  } ?>>Associate Professor</option>
                </select>
                <?php
                if (isset($_POST['designation']) && !validate_field($_POST['designation'])) {
                  ?>
                  <p class="text-danger my-1">Select Designation!</p>
                  <?php
                }
                ?>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary">Cancel</button>
            <button type="submit" name="add_faculty" class="btn brand-bg-color">Submit</button>
          </div>
        </form>
      </div>

    </main>
  </div>

  <script src="./js/main.js"></script>
  <script src="./js/curriculum-table.js"></script>
  <script>
    function capitalizeFirstLetter(input) {
      input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
    }
  </script>

</body>

</html>