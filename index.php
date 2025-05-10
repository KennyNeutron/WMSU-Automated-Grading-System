<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login');
}

require_once './classes/faculty_subs.class.php';
require_once './classes/profiling.class.php';
$user_profiling = new Profiling();
$fac_subs = new Faculty_Subjects();

$all_subs = $fac_subs->getByUser($_SESSION['emp_id']);
$info = $user_profiling->fetchEMP($_SESSION['emp_id']);

$schoolYears = [];
$semesters = [];

// Collect unique school years and semesters
foreach ($all_subs as $sub) {
  if (!in_array($sub['school_yr'], $schoolYears)) {
    $schoolYears[] = $sub['school_yr'];
  }
  if (!in_array($sub['semester'], $semesters)) {
    $semesters[] = $sub['semester'];
  }
}

// Sort for better user experience
rsort($schoolYears); // Descending order (latest first)
sort($semesters); // Ascending order

// Get selected filters
$selectedYear = $_GET['school_yr'] ?? null;
$selectedSemester = $_GET['semester'] ?? null;

// Filter subjects based on selection
$filteredSubs = array_filter($all_subs, function ($sub) use ($selectedYear, $selectedSemester) {
  return (!$selectedYear || $sub['school_yr'] == $selectedYear) &&
    (!$selectedSemester || $sub['semester'] == $selectedSemester);
});

// Sort filtered results by school year (descending) and semester (ascending)
usort($filteredSubs, function ($a, $b) {
  return ($b['school_yr'] <=> $a['school_yr']) ?: ($a['semester'] <=> $b['semester']);
});

?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Home';
$home_page = 'active';
include './includes/head.php';
?>

<body>
  <div class="home">
    <div class="side">
      <?php require_once('./includes/sidepanel.php') ?>
    </div>
    <main>
      <div class="header">
        <?php require_once('./includes/header.php') ?>
      </div>

      <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
        <div class="d-flex align-items-center">
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">Subject Assigned</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="details">
          <p class="fw-bolder">Name: <span class="fw-bold brand-color"><?= ucwords($_SESSION['name']) . '(' . $_SESSION['emp_id'] . ')' ?></span></p>
          <p class="fw-bolder">Designation: <span
              class="fw-bold brand-color"><?= $info ? ucwords($info['designation']) : 'N/A' ?></span></p>
          <p class="fw-bolder">Academic Rank: <span
              class="fw-bold brand-color"><?= $info ? ucwords($info['acad_type']) : 'N/A' ?></span></p>
          <p class="fw-bolder">Faculty Type: <span
              class="fw-bold brand-color"><?= $info ? ucwords($info['faculty_type']) : 'N/A' ?></span></p>
        </div>

        <div class="content container-fluid mw-100 border rounded shadow p-3">
          <div class="btn-toolbar d-flex justify-content-end">
            <div class="btn-group gap-3">
              <div class="dropdown">
                <button type="button" class="btn border border-danger dropdown-toggle" data-bs-toggle="dropdown">
                  <?= $selectedYear ? "S.Y. " . $selectedYear : "Select School Year" ?>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="?">Default</a></li>
                  <?php foreach ($schoolYears as $year): ?>
                    <li>
                      <a class="dropdown-item school-year-option" href="?" data-year="<?= $year ?>"
                        data-first-semester="<?= $semesters[0] ?>">
                        <?= "S.Y. " . $year ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>

              <div class="dropdown">
                <button type="button" class="btn border border-danger dropdown-toggle" data-bs-toggle="dropdown">
                  <?= $selectedSemester ? $selectedSemester : ($selectedYear ? $semesters[0] : "Select Semester") ?>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="?<?= $selectedYear ? "school_yr=$selectedYear" : "" ?>">Default</a>
                  </li>
                  <?php foreach ($semesters as $sem): ?>
                    <li><a class="dropdown-item"
                        href="?<?= $selectedYear ? "school_yr=$selectedYear&" : "" ?>semester=<?= $sem ?>"><?= $sem ?></a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>

            </div>
          </div>

          <hr>

          <div class="d-flex flex-column align-items-center">
            <h3><?= $selectedYear ? "S.Y. " . $selectedYear : "All" ?></h3>
            <h4><?= $selectedSemester ? $selectedSemester : ($selectedYear ? $semesters[0] : "") ?></h4>
          </div>

          <table id="home_table" class="table table-striped" style="width:125%">
            <thead>
              <tr>
                <th rowspan="2" class="align-middle">#</th>
                <th rowspan="2" class="align-middle">Subject Code</th>
                <th rowspan="2" class="align-middle">Name</th>
                <th rowspan="2" class="align-middle">Prerequisite</th>
                <th rowspan="2" class="align-middle">Year/Section</th>
                <th rowspan="2" class="align-middle"># of Students</th>
                <th colspan="2" class="text-center">Room</th>
                <th colspan="2" class="text-center">Schedules</th>
                <th colspan="3" class="text-center">Units</th>
              </tr>
              <tr>
                <th>Lecture</th>
                <th>Laboratory</th>
                <th>Lecture</th>
                <th>Laboratory</th>
                <th>Lec</th>
                <th>Lab</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $counter = 1;
              function formatValue($value)
              {
                return !empty($value) ? $value : "<span style='color: gray;'>N/A</span>";
              }

              foreach ($filteredSubs as $sub):
                $sub_pre = formatValue($sub['sub_prerequisite']);
                $lec_room = formatValue($sub['lec_room']);
                $lab_room = formatValue($sub['lab_room']);
                $lec_days = $sub['lec_days'];
                $lec_time = formatValue($sub['lec_time']);
                $lab_days = $sub['lab_days'];
                $lab_time = formatValue($sub['lab_time']);
                $lec_units = isset($sub['lec_units']) ? $sub['lec_units'] : 0;
                $lab_units = isset($sub['lab_units']) ? $sub['lab_units'] : 0;
                $total_units = $lec_units + $lab_units;
                ?>
                <tr>
                  <td><?= $counter ?></td>
                  <td><a href="subject_students.php?faculty_sub_id=<?= $sub['faculty_sub_id'] ?>"><?= $sub['sub_code'] ?></a></td>
                  <td><?= $sub['sub_name'] ?></td>
                  <td><?= $sub_pre ?></td>
                  <td><?= $sub['yr_sec'] ?></td>
                  <td><?= $sub['no_students'] ?></td>
                  <td><?= $lec_room ?></td>
                  <td><?= $lab_room ?></td>
                  <td><?= $lec_days . " (" . $lec_time . ")" ?></td>
                  <td><?= $lab_days . " (" . $lab_time . ")" ?></td>
                  <td><?= $lec_units ?></td>
                  <td><?= $lab_units ?></td>
                  <td><?= $total_units ?></td>
                </tr>
                <?php
                $counter++;
              endforeach;
              ?>
            </tbody>
          </table>

        </div>
      </div>
    </main>

    <?php if (!$info): ?>
      <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-warning text-black">
              <h5 class="modal-title" id="approvalModalLabel">Account Pending Approval</h5>
            </div>
            <div class="modal-body text-center">
              <p>Your account is pending for approval.</p>
              <p>Please contact the administrator.</p>
            </div>
            <div class="modal-footer justify-content-end">
              <a href="./logout.php" class="btn btn-danger">Logout</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <script src="./js/main.js"></script>
  <script src="./js/index_table.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var myModal = new bootstrap.Modal(document.getElementById('approvalModal'), {
        backdrop: 'static',
        keyboard: false
      });
      myModal.show();
    });

    document.addEventListener("DOMContentLoaded", function () {
      const schoolYearOptions = document.querySelectorAll(".school-year-option");

      schoolYearOptions.forEach(option => {
        option.addEventListener("click", function (e) {
          e.preventDefault();

          const selectedYear = this.getAttribute("data-year");
          const firstSemester = this.getAttribute("data-first-semester");

          window.location.href = `?school_yr=${selectedYear}&semester=${firstSemester}`;
        });
      });
    });

  </script>
</body>

</html>