<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login.php');
  exit();
}

require_once './classes/faculty_subs.class.php';
require_once './classes/period.class.php';
require_once './classes/component.class.php';
require_once './classes/grades.class.php';

$selected_faculty_sub_id = isset($_POST['faculty_sub_id']) ? $_POST['faculty_sub_id'] : null;

$fac_subs = new Faculty_Subjects();
$period = new Periods();
$components = new SubjectComponents();
$studentsBySub = new Grades();

$all_subs = $fac_subs->getByUser($_SESSION['emp_id']);
$subject = $fac_subs->getProf($selected_faculty_sub_id);
$studentList = $studentsBySub->showBySubject($selected_faculty_sub_id);
$sub_type = "";

if ($subject['subject_type'] == 'lecture') {
  $sub_type = ' - LEC';
} elseif ($subject['subject_type'] == 'laboratory') {
  $sub_type = ' - LAB';
} elseif ($subject['subject_type'] == 'combined') {
  $sub_type = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = $subject['sub_code'] . " (" . $subject['yr_sec'] . ') - Student List';
$student_page = 'active';
include './includes/head.php';
?>

<body>
  <div class="home">
    <div class="side">
      <?php
      require_once('./includes/sidepanel.php');
      ?>
    </div>
    <main>
      <div class="header">
        <?php
        require_once('./includes/header.php');
        ?>
      </div>

      <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
        <div class="d-flex align-items-center">
          <a href="./select_subject_students" class="bg-none">
            <i class='bx bx-chevron-left fs-2 brand-color'></i>
          </a>

          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">Student List</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <!-- <div class="row row-cols-1 row-cols-sm-1 row-cols-md-4">
          <div class="col dropdown">
            <form id="facultyForm" method="POST" action="">
              <select name="faculty_sub_id" class="btn border dropdown-toggle form-select border-danger mb-4"
                onchange="document.getElementById('facultyForm').submit();">
                <?php
                foreach ($all_subs as $sub):
                  $selected = ($sub['faculty_sub_id'] == $selected_faculty_sub_id) ? 'selected' : '';
                ?>
                  <option value="<?= $sub['faculty_sub_id'] ?>" <?= $selected ?>>
                    <?= $sub['sub_code'] ?>
                  </option>
                  <?php
                endforeach;
                  ?>
              </select>
            </form>
          </div>
        </div> -->

        <?php
        if (!empty($_POST['faculty_sub_id'])):
        ?>
          <div class="d-flex flex-column align-items-center position-relative">
            <h3 class="brand-color"><?= $subject ? ucwords($subject['sub_name']) : '' ?></h3>

            <h4 class="mb-0"><?= $subject ? $subject['sub_code'] . $sub_type : "" ?></h4>

            <a href="./students_ranking?faculty_sub_id=<?= $selected_faculty_sub_id ?>"
              class="btn brand-bg-color position-absolute end-0 top-0 my-2">
              Students Ranking
            </a>

            <h4 style="margin: 0; padding: 0;">(<?= $subject ? $subject['yr_sec'] : "" ?>)</h4>
          </div>

          <div class="search-keyword col-12 flex-lg-grow-0 d-flex justify-content-between gap-3 my-4 px-2">
            <div class="d-flex justify-content-between gap-1">
              <div id="MyButtons" class="d-flex me-1 mb-md-2 mb-lg-0 col-12 col-md-auto"></div>
              <?php if ($subject['subject_type'] === 'laboratory'): ?>
                <!-- <a href="./students_complete_grade?faculty_sub_id=<?= $selected_faculty_sub_id ?>" class="btn brand-bg-color">View
                Complete Grades</a> -->
                <a href="./students_ranking?faculty_sub_id=<?= $selected_faculty_sub_id ?>"
                  class="btn brand-bg-color">Students Ranking</a>

              <?php endif; ?>

              <?php if ($subject['subject_type'] === 'lecture' || $subject['subject_type'] === 'combined'): ?>
                <!-- <a href="./students_complete_grade?faculty_sub_id=<?= $selected_faculty_sub_id ?>" class="btn brand-bg-color">View
                  Complete Grades</a> -->


                <a href="./student_grade_midterm?faculty_sub_id=<?= $selected_faculty_sub_id ?>"
                  class="btn brand-bg-color">Midterm Grade</a>


                <a href="./student_grade_finalterm?faculty_sub_id=<?= $selected_faculty_sub_id ?>"
                  class="btn brand-bg-color">Finalterm Grade</a>


                <!-- <a href="./students_ranking?faculty_sub_id=<?= $selected_faculty_sub_id ?>"
                  class="btn brand-bg-color">Students Ranking</a> -->


                <a href="./student_grade_incomplete?faculty_sub_id=<?= $selected_faculty_sub_id ?>"
                  class="btn brand-bg-color">Complete Grade</a>
              <?php endif; ?>
            </div>
            <div class="input-group" style="width: 40% !important;">
              <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control">
              <button class="btn btn-outline-secondary brand-bg-color" type="button"><i class='bx bx-search'
                  aria-hidden="true"></i></button>
            </div>
          </div>

          <table id="students" class="table table-striped table-sm" style="width:100%">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Full Name (Last, First M.I.)</th>
                <th scope="col">Student ID</th>
                <th scope="col">Email</th>
                <th scope="col">Year & Section</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $counter = 1;
              foreach ($studentList as $item) {
              ?>
                <tr>
                  <td><?= $counter ?></td>
                  <td><?= $item['fullName'] ?></td>
                  <td><?= $item['student_id'] ?></td>
                  <td><?= $item['email'] ?></td>
                  <td><?= $item['year_section'] ?></td>
                </tr>
              <?php
                $counter++;
              }
              ?>
            </tbody>
          </table>
        <?php
        endif;
        ?>
      </div>
    </main>
  </div>

  <?php
  require_once('./includes/js.php');
  ?>
  <script src="./js/student_table.js"></script>
</body>

</html>