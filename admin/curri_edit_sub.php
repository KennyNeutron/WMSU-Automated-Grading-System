<?php 
require_once '../tools/functions.php';
require_once '../classes/curri_page.class.php';
require_once '../classes/user.class.php';
require_once '../classes/curr_year.class.php'; 

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login');
}

if(isset($_GET['curr_id'])){
  $curr_table = new Curr_table();
  $record = $curr_table->fetch($_GET['curr_id']);
  $curr_table->curr_year_id = $record['curr_year_id'];
  $curr_table->college_course_id = $record['college_course_id'];
  $curr_table->time_id = $record['time_id'];
  $curr_table->year_level_id = $record['year_level_id'];
  $curr_table->semester_id = $record['semester_id'];
  $curr_table->sub_code = $record['sub_code'];
  $curr_table->sub_name = $record['sub_name'];
  $curr_table->sub_prerequisite = $record['sub_prerequisite'];
  $curr_table->lec = $record['lec'];
  $curr_table->lab = $record['lab'];
}

if (isset($_POST['edit_curr_sub'])) {
  try {
    $user = new User();
    $record = $user->fetch($_SESSION['user_id']);
    $user->user_id = $_SESSION['user_id'];

    $curr_table = new Curr_table();
    //sanitize
    $curr_table->curr_id = $_GET['curr_id'];
    $curr_table->curr_year_id = htmlentities($_POST['curr_year_id']);
    $curr_table->college_course_id = htmlentities($_POST['college_course_id']);
    $curr_table->time_id = htmlentities($_POST['time_id']);
    $curr_table->year_level_id = htmlentities($_POST['year_level_id']);
    $curr_table->semester_id = htmlentities($_POST['semester_id']);
    $curr_table->sub_code = htmlentities($_POST['sub_code']);
    $curr_table->sub_name = htmlentities($_POST['sub_name']);
    $curr_table->sub_prerequisite = htmlentities($_POST['sub_prerequisite']);
    $curr_table->lec = htmlentities($_POST['lec']);
    $curr_table->lab = htmlentities($_POST['lab']);

    $curr_year = new Curr_year();

    // Validation
    $errors = [];
    if (!validate_field($curr_table->sub_code)) {
      $errors[] = 'Please enter Subject Code';
    }
    if ($curr_table->is_subcode_exist($curr_table->sub_code)) {
      $errors[] = 'Subject Code already exists';
    }
    if (!validate_field($curr_table->sub_name)) {
      $errors[] = 'Please enter Subject Name';
    }
    if (!validate_field($curr_table->sub_prerequisite)) {
      $errors[] = 'Please enter Subject Prerequisite';
    }
    if (!validate_field($curr_table->lec)) {
      $errors[] = 'Please enter Lecture';
    }
    if (!validate_field($curr_table->lab)) {
      $errors[] = 'Please enter Laboratory';
    }

    if (empty($errors)) {
      if ($curr_table->edit()) {
        $redirect_url = './curri_page?year_id=' . $_GET['year_id'] . '&course_id=' . $_GET['course_id'] . '&time_id=' . $_GET['time_id'] . '&year_level_id=' . $_GET['year_level_id'] . '&semester_id=' . $_GET['semester_id'];
        header('location: ' . $redirect_url);
        exit;
      } else {
        $message = 'Something went wrong editing subject.';
      }
    } else {
      throw new Exception(implode('<br>', $errors));
    }
  } catch (Exception $e) {
    $error_message = $e->getMessage();
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<?php 
$title = 'Edit Subject';
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
      <div class="header" >
        <?php
        require_once('../includes/admin_header.php')
        ?>
      </div>
      
      <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;" >
        <div class="d-flex align-items-center">
          <button onclick="history.back()" class="bg-none d-flex align-items-center" ><i class='bx bx-chevron-left fs-2 brand-color'></i></button>
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">Computer Science Curriculum</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <form action="#" method="post">
          <?php
          if (isset($error_message)) {
            echo "<div class='alert alert-danger'>$error_message</div>";
          }
          ?>
          <div class="row row-cols-1 row-cols-md-2 align-items-start">
            <div class="col">
              <div class="mb-3">
                <label for="sub_code" class="form-label">Code</label>
                <input type="text" class="form-control" id="sub_code" aria-describedby="sub_code" name="sub_code" value="<?php if (isset($_POST['sub_code'])) {
                                                                                                                                 echo $_POST['sub_code'];
                                                                                                                               } else if (isset($curr_table->sub_code)) {
                                                                                                                                 echo $curr_table->sub_code;
                                                                                                                               } ?>">
              </div>

              <div class="mb-3">
                <label for="sub_name" class="form-label">Description</label>
                <input type="text" class="form-control" id="sub_name" aria-describedby="sub_name" name="sub_name" value="<?php if (isset($_POST['sub_name'])) {
                                                                                                                                 echo $_POST['sub_name'];
                                                                                                                               } else if (isset($curr_table->sub_name)) {
                                                                                                                                 echo $curr_table->sub_name;
                                                                                                                               } ?>">
              </div>

              <div class="mb-3">
                <label for="sub_prerequisite" class="form-label">Prerequisite</label>
                <input type="text" class="form-control" id="sub_prerequisite" aria-describedby="sub_prerequisite" name="sub_prerequisite" value="<?php if (isset($_POST['sub_prerequisite'])) {
                                                                                                                                                         echo $_POST['sub_prerequisite'];
                                                                                                                                                       } else if (isset($curr_table->sub_prerequisite)) {
                                                                                                                                                         echo $curr_table->sub_prerequisite;
                                                                                                                                                       } ?>">
              </div>      
            </div>

            <div class="col">
              <div class="mb-3">
                <label for="lec" class="form-label">Lecture</label>
                <input type="number" class="form-control" id="lec" aria-describedby="lec" name="lec" value="<?php if (isset($_POST['lec'])) {
                                                                                                                    echo $_POST['lec'];
                                                                                                                  } else if (isset($curr_table->lec)) {
                                                                                                                    echo $curr_table->lec;
                                                                                                                  } ?>">
              </div>

              <div class="mb-3">
                <label for="lab" class="form-label">Laboratory</label>
                <input type="number" class="form-control" id="lab" aria-describedby="lab" name="lab" value="<?php if (isset($_POST['lab'])) {
                                                                                                                    echo $_POST['lab'];
                                                                                                                  } else if (isset($curr_table->lab)) {
                                                                                                                    echo $curr_table->lab;
                                                                                                                  } ?>">
              </div>

              <div class="mb-3">
                <label for="total_unit" class="form-label">Total Unit</label>
                <input type="number" class="form-control" id="total_unit" aria-describedby="total_unit" disabled  value="<?php if (isset($_POST['total_unit'])) {
                                                                                                                          echo $_POST['total_unit'];
                                                                                                                        } ?>">
              </div>
              
              <div class="mb-3 d-none">
                <label for="curr_year_id" class="form-label">year-id</label>
                <input type="number" class="form-control" id="curr_year_id" name="curr_year_id" aria-describedby="curr_year_id" value="<?php if (isset($_POST['curr_year_id'])) {
                                                                                                                                               echo $_POST['curr_year_id'];
                                                                                                                                             } else if (isset($curr_table->curr_year_id)) {
                                                                                                                                               echo $curr_table->curr_year_id;
                                                                                                                                             } ?>">
              </div>
              <div class="mb-3 d-none">
                <label for="college_course_id" class="form-label">course-id</label>
                <input type="number" class="form-control" id="college_course_id" name="college_course_id" aria-describedby="college_course_id" value="<?php if (isset($_POST['college_course_id'])) {
                                                                                                                                                              echo $_POST['college_course_id'];
                                                                                                                                                            } else if (isset($curr_table->college_course_id)) {
                                                                                                                                                              echo $curr_table->college_course_id;
                                                                                                                                                            } ?>">
              </div>
              <div class="mb-3 d-none">
                <label for="time_id" class="form-label">time_id</label>
                <input type="number" class="form-control" id="time_id" name="time_id" aria-describedby="time_id" value="<?php if (isset($_POST['time_id'])) {
                                                                                                                                echo $_POST['time_id'];
                                                                                                                              } else if (isset($curr_table->time_id)) {
                                                                                                                                echo $curr_table->time_id;
                                                                                                                              } ?>">
              </div>
              <div class="mb-3 d-none">
                <label for="year_level_id" class="form-label">year_level_id</label>
                <input type="number" class="form-control" id="year_level_id" name="year_level_id" aria-describedby="year_level_id" value="<?php if (isset($_POST['year_level_id'])) {
                                                                                                                                                  echo $_POST['year_level_id'];
                                                                                                                                                } else if (isset($curr_table->year_level_id)) {
                                                                                                                                                  echo $curr_table->year_level_id;
                                                                                                                                                } ?>">
              </div>
              <div class="mb-3 d-none">
                <label for="semester_id" class="form-label">semester_id</label>
                <input type="number" class="form-control" id="semester_id" name="semester_id" aria-describedby="semester_id" value="<?php if (isset($_POST['semester_id'])) {
                                                                                                                                            echo $_POST['semester_id'];
                                                                                                                                          } else if (isset($curr_table->semester_id)) {
                                                                                                                                            echo $curr_table->semester_id;
                                                                                                                                          } ?>">
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" onclick="cancelEdit();" class="btn btn-secondary">Cancel</button>
            <button type="submit" id="edit_curr_sub" name="edit_curr_sub" class="btn brand-bg-color">Save</button>
          </div>
        </form>
      </div>

    </main>
  </div>
  
  <script src="./js/main.js"></script>

  <script>
  $(document).ready(function () {
    $('#lec, #lab').on('input', function () {
      var lec = parseInt($('#lec').val()) || 0;
      var lab = parseInt($('#lab').val()) || 0;
      var totalUnit = lec + lab;
      $('#total_unit').val(totalUnit);
    });
  });
</script>

<script>
  function cancelEdit() {
    var yearId = encodeURIComponent('<?php echo $_GET['year_id']; ?>');
    var courseId = encodeURIComponent('<?php echo $_GET['course_id']; ?>');
    var timeId = encodeURIComponent('<?php echo $_GET['time_id']; ?>');
    var yearLevelId = encodeURIComponent('<?php echo $_GET['year_level_id']; ?>');
    var semesterId = encodeURIComponent('<?php echo $_GET['semester_id']; ?>');

    var redirectUrl = './curri_page.php?year_id=' + yearId + '&course_id=' + courseId + '&time_id=' + timeId + '&year_level_id=' + yearLevelId + '&semester_id=' + semesterId;

    window.location.href = redirectUrl;
  }
</script>

  
</body>
</html>