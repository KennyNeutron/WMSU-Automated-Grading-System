<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login');
}

require_once '../classes/department.class.php';
require_once '../classes/faculty_sched.class.php';

$dept = new Department();
$deptName = $dept->showName($_GET['department_id']);

$sched = new Faculty_Sched();
$schedArray1 = $sched->show1($_GET['department_id']);
$schedArray2 = $sched->show2($_GET['department_id']);
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Faculty';
$faculty_page = 'active';
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
          <a href="./faculty_course_select" class="bg-none d-flex align-items-center"><i
              class='bx bx-chevron-left fs-2 brand-color'></i></a>
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">FACULTY</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="content mw-100 rounded shadow py-3">
          <div class="text-center mb-3">
            <h2><?= $deptName['department_name']; ?></h2>
          </div>
          <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <button class="nav-link text-dark active" id="nav-regular-tab" data-bs-toggle="tab"
                data-bs-target="#nav-regular" type="button" role="tab" aria-controls="nav-regular"
                aria-selected="true">Regular Lecturer</button>
              <button class="nav-link text-dark" id="nav-visiting-tab" data-bs-toggle="tab"
                data-bs-target="#nav-visiting" type="button" role="tab" aria-controls="nav-visiting"
                aria-selected="false">Visiting Lecturer</button>
            </div>
          </nav>
          <div class="tab-content py-4 px-3" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-regular" role="tabpanel" aria-labelledby="nav-regular-tab">
              <div class="search-keyword col-12 flex-lg-grow-0 d-flex my-2 px-2">

                <div class="form-group col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0 ms-lg-auto">
                  <?php
                  // Extract unique school years from both arrays
                  $schoolYears = array_unique(array_merge(
                    array_column($schedArray2, 'school_yr')
                  ));

                  // Sort in descending order (latest school year first)
                  rsort($schoolYears);
                  ?>
                  <select name="school_year" id="school_year" class="form-select me-md-2">
                    <option value="">School Year</option>
                    <?php foreach ($schoolYears as $year): ?>
                      <option value="<?= htmlspecialchars($year) ?>"><?= htmlspecialchars($year) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group mx-4 col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0">
                  <?php
                  $sems = array_unique(array_merge(
                    array_column($schedArray2, 'semester')
                  ));

                  rsort($sems);
                  ?>
                  <select name="semester" id="semester" class="form-select me-md-2">
                    <option value="">Semester</option>
                    <?php foreach ($sems as $sem): ?>
                      <option value="<?= htmlspecialchars($sem) ?>"><?= htmlspecialchars($sem) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="input-group">
                  <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control">
                  <button class="btn btn-outline-secondary brand-bg-color" type="button"><i class='bx bx-search'
                      aria-hidden="true"></i></button>
                </div>
                <a href="./add_schedule?department_id=<?= $_GET['department_id'] ?>"
                  class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i
                    class='bx bx-plus-circle'></i></a>
              </div>

              <table id="main_faculty_regular" class="table table-striped table-sm" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Employee ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Academic Rank</th>
                    <th>Designation</th>
                    <th>Number of hours per week</th>
                    <th>School Year</th>
                    <th>Semester</th>
                    <th width="5%">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  foreach ($schedArray2 as $item) {
                    ?>
                    <tr>
                      <td><?= $counter ?></td>
                      <td><a
                          href="./faculty_schedule?sched_id=<?= $item['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>"><?= $item['emp_id'] ?? 'N/A' ?></a>
                      </td>
                      <td><?= $item['fullName'] ?? 'N/A' ?></td>
                      <td><?= $item['email'] ?? 'N/A' ?></td>
                      <td><?= $item['academic_rank'] ?? 'N/A' ?></td>
                      <td><?= $item['designation'] ?? 'N/A' ?></td>
                      <td><?= $item['hrs_per_week'] ?? 'N/A' ?></td>
                      <td><?= $item['school_yr'] ?? 'N/A' ?></td>
                      <td><?= $item['semester'] ?? 'N/A' ?></td>
                      <td class="text-center">
                        <a
                          href="./edit_schedule?sched_id=<?= $item['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>"><i
                            class='bx bx-edit text-success fs-4'></i></a>
                        <button class="delete-btn bg-none" data-subject-id="<?= $item['sched_id'] ?>">
                          <i class='bx bx-trash-alt text-danger fs-4'></i>
                        </button>
                      </td>
                    </tr>
                    <?php
                    $counter++;
                  }
                  ?>
                </tbody>
              </table>
            </div>

            <div class="tab-pane fade" id="nav-visiting" role="tabpanel" aria-labelledby="nav-visiting-tab">
              <div class="search-keyword col-12 flex-lg-grow-0 d-flex my-2 px-2">
                <div class="form-group col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0 ms-lg-auto">
                  <?php
                  $schoolYears = array_unique(array_merge(
                    array_column($schedArray1, 'school_yr')
                  ));

                  rsort($schoolYears);
                  ?>
                  <select name="school_year" id="school_year" class="form-select me-md-2">
                    <option value="">School Year</option>
                    <?php foreach ($schoolYears as $year): ?>
                      <option value="<?= htmlspecialchars($year) ?>"><?= htmlspecialchars($year) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group mx-4 col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0">
                  <?php
                  $sems = array_unique(array_merge(
                    array_column($schedArray1, 'semester')
                  ));

                  rsort($sems);
                  ?>
                  <select name="semester" id="semester" class="form-select me-md-2">
                    <option value="">Semester</option>
                    <?php foreach ($sems as $sem): ?>
                      <option value="<?= htmlspecialchars($sem) ?>"><?= htmlspecialchars($sem) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="input-group">
                  <input type="text" name="keyword1" id="keyword1" placeholder="Search" class="form-control">
                  <button class="btn btn-outline-secondary brand-bg-color" type="button"><i class='bx bx-search'
                      aria-hidden="true"></i></button>
                </div>
                <a href="./add_schedule?department_id=<?= $_GET['department_id'] ?>"
                  class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i
                    class='bx bx-plus-circle'></i></a>
              </div>

              <table id="main_faculty_visiting" class="table table-striped table-sm" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Employee ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Academic Rank</th>
                    <th>Designation</th>
                    <th>Number of hours per week</th>
                    <th>School Year</th>
                    <th>Semester</th>
                    <th width="5%">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  foreach ($schedArray1 as $item) {
                    ?>
                    <tr>
                      <td><?= $counter ?></td>
                      <td><a
                          href="./faculty_schedule?sched_id=<?= $item['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>"><?= $item['emp_id'] ?? 'N/A' ?></a>
                      </td>
                      <td><?= $item['fullName'] ?? 'N/A' ?></td>
                      <td><?= $item['email'] ?? 'N/A' ?></td>
                      <td><?= $item['academic_rank'] ?? 'N/A' ?></td>
                      <td><?= $item['designation'] ?? 'N/A' ?></td>
                      <td><?= $item['hrs_per_week'] ?? 'N/A' ?></td>
                      <td><?= $item['school_yr'] ?? 'N/A' ?></td>
                      <td><?= $item['semester'] ?? 'N/A' ?></td>
                      <td class="text-center">
                        <a
                          href="./edit_schedule?sched_id=<?= $item['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>"><i
                            class='bx bx-edit text-success fs-4'></i></a>
                        <button class="delete-btn bg-none" data-subject-id="<?= $item['sched_id'] ?>">
                          <i class='bx bx-trash-alt text-danger fs-4'></i>
                        </button>
                      </td>
                    </tr>
                    <?php
                    $counter++;
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div id="alertContainer"></div>
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete this schedule?</p>
              <p class="text-danger"><strong>Warning:</strong> This will also delete all existing data, including
                subjects, students, and grades associated with this schedule.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="./js/main.js"></script>
  <script src="./js/faculty_table.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Function to save the active tab to localStorage
      function saveActiveTab(tabId) {
        localStorage.setItem('activeTab', tabId);
      }

      // Function to restore the active tab from localStorage
      function restoreActiveTab() {
        const activeTabId = localStorage.getItem('activeTab');
        if (activeTabId) {
          const tabTrigger = new bootstrap.Tab(document.querySelector(`#${activeTabId}`));
          tabTrigger.show();
        }
      }

      // Attach event listeners to tabs
      const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
      tabButtons.forEach(button => {
        button.addEventListener('click', function () {
          const tabId = this.getAttribute('id'); // Get the ID of the clicked tab
          saveActiveTab(tabId); // Save the active tab ID
        });
      });

      // Restore the active tab on page load
      restoreActiveTab();

      const deleteButtons = document.querySelectorAll('.delete-btn');
      const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
      let currentSchedId = null;

      deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
          currentSchedId = this.getAttribute('data-subject-id');
          deleteModal.show();
        });
      });

      document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (currentSchedId) {
          fetch('./delete_schedule.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ sched_id: currentSchedId }),
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
              } else {
                alert(data.message);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert('An error occurred. Please try again.');
            });
        }
      });

      function showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        const alertHTML = `
          <div class="alert alert-${type} d-flex flex-row align-items-center gap-2 position-fixed top-0 start-50 translate-middle-x w-auto mt-4 z-index-1050" role="alert">
            <strong>${type === 'success' ? `Successfully Deleted! <i class='bx bx-check-circle' ></i>` : 'Error!'}</strong> ${message}
          </div>
        `;
        alertContainer.innerHTML = alertHTML;
      }
    });
  </script>
</body>

</html>