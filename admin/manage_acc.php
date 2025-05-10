<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login');
}

require_once '../classes/user.class.php';
require_once '../classes/profiling.class.php';
require_once '../tools/functions.php';

?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Manage Accounts';
$userfaculty_page = 'active';
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
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">Manage Accounts</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="content container-fluid mw-100 border rounded shadow p-3">

          <div class="search-keyword col-12 flex-lg-grow-0 d-flex justify-content-between">

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
            
            <div class="input_width d-flex gap-2">
              <div class="form-group col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0">
                <select name="department" id="department" class="form-select me-md-2">
                  <option value="" disabled selected>Department</option>
                  <?php
                  if ($department_array) {
                    foreach ($department_array as $item) {
                      ?>
                      <option value="<?= $item['department_name'] ?>">
                        <?php echo $item['department_name'] ?>
                      </option>
                      <?php
                    }
                  }
                  ?>
                </select>
              </div>

              <div class="form-group col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0">
                <select name="status" id="status" class="form-select me-md-2">
                  <option value="" disabled selected>Status</option>
                  <option style="color:red;" value="Pending Approval">Pending Approval</option>
                  <option style="color:green;" value="Approved">Approved</option>
                </select>
              </div>
            </div>

            <div class="input_width d-flex" style="width: 40% !important;">
              <div class="input-group">
                <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control">
                <button class="btn btn-outline-secondary brand-bg-color" type="button"><i class='bx bx-search'
                    aria-hidden="true"></i></button>
              </div>
              <a href="./add_user" class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i
                  class='bx bx-plus-circle'></i></a>
            </div>

          </div>
          <hr>
          <?php
          $profiling = new Profiling();
          $user = new User();
          $userArray = $user->show();
          ?>
          <table id="manage_acc" class="table table-striped table-sm" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Designation</th>
                <th>Academic Rank</th>
                <th>Department</th>
                <th>Faculty Type</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $counter = 1;
              foreach ($userArray as $item) {
                if ($item['user_role'] == '1') {
                  // Default values
                  $designation = 'N/A';
                  $acad_type = 'N/A';
                  $department = 'N/A';
                  $faculty_type = $item['faculty_type'];
                  $status = 'Pending Approval';

                  // Combine names into Full Name
                  $lastName = !empty($item['l_name']) ? $item['l_name'] : 'N/A';
                  $firstName = !empty($item['f_name']) ? $item['f_name'] : '';
                  $middleName = !empty($item['m_name']) ? $item['m_name'] : '';

                  $fullName = trim($lastName . ', ' . $firstName . ' ' . $middleName);

                  // Check if emp_id exists in profiling
                  if (!empty($item['emp_id']) && $profiling->is_empId_exist($item['emp_id'])) {
                    $profileData = $profiling->get_emp_details($item['emp_id']);
                    if ($profileData) {
                      $designation = !empty($profileData['designation']) ? $profileData['designation'] : $designation;
                      $acad_type = !empty($profileData['acad_type']) ? $profileData['acad_type'] : $acad_type;
                      $faculty_type = !empty($profileData['faculty_type']) ? $profileData['faculty_type'] : $faculty_type;
                      $department = !empty($profileData['department_name']) ? $profileData['department_name'] : $department;
                      $status = 'Approved';
                    }
                  }
                  ?>

                  <tr>
                    <td><?= $counter ?></td>
                    <td style="color: <?= empty($item['emp_id']) ? 'grey' : 'inherit' ?>;">
                      <?= !empty($item['emp_id']) ? $item['emp_id'] : 'N/A' ?>
                    </td>
                    <td style="color: <?= $fullName == 'N/A' ? 'grey' : 'inherit' ?>;">
                      <?= $fullName ?>
                    </td>
                    <td style="color: <?= empty($item['email']) ? 'grey' : 'inherit' ?>;">
                      <?= !empty($item['email']) ? $item['email'] : 'N/A' ?>
                    </td>
                    <td style="color: <?= $designation == 'N/A' ? 'grey' : 'inherit' ?>;">
                      <?= $designation ?>
                    </td>
                    <td style="color: <?= $acad_type == 'N/A' ? 'grey' : 'inherit' ?>;">
                      <?= $acad_type ?>
                    </td>
                    <td style="color: <?= $department == 'N/A' ? 'grey' : 'inherit' ?>;">
                      <?= $department ?>
                    </td>
                    <td style="color: <?= $faculty_type == 'N/A' ? 'grey' : 'inherit' ?>;">
                      <?= $faculty_type ?>
                    </td>
                    <td>
                      <span class="badge rounded-pill <?= $status == 'Pending Approval' ? 'bg-warning' : 'bg-success' ?>"
                        style="<?= $status == 'Pending Approval' ? 'color: #555555;' : 'color: white;' ?>"><?= $status ?></span>
                    </td>
                    <td>
                      <a href="approve_user.php?id=<?= $item['user_id'] ?>" class="btn btn-sm btn-primary"
                        style="<?= $status == 'Pending Approval' ? '' : 'display:none;' ?>"><i
                          class='bx bx-check-circle'></i>
                        Approve</a>
                      <button class="decline-btn btn-sm btn-danger" data-subject-id="<?= $item['user_id'] ?>"
                        style="<?= $status == 'Pending Approval' ? '' : 'display:none;' ?>"><i class='bx bx-x-circle'></i>
                        Decline</button>
                    </td>
                  </tr>

                  <?php
                  $counter++;
                }
              }
              ?>
            </tbody>
          </table>

        </div>

      </div>


      <!-- confirm decline modal markup -->
      <div class="modal fade" id="declineConfirmationModal" tabindex="-1"
        aria-labelledby="declineConfirmationModalLabel" aria-hidden="true">
        <div id="alertContainer"></div>
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteConfirmationModalLabel">Decline Approval</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to decline this user approval?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger" id="confirmDeclineBtn">Yes</button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="./js/main.js"></script>
  <script>
    $(document).ready(function () {
      dataTable = $("#manage_acc").DataTable({
        dom: "Brtp",
        responsive: true,
        pageLength: 10,
        buttons: [
          {
            remove: "true",
          },
        ],
        columnDefs: [
          { targets: [1, 3, 9], orderable: false },
        ],
      });

      var table = dataTable;
      var filter = createFilter(table, [1, 2]);

      function createFilter(table, columns) {
        var input = $("input#keyword").on("keyup", function () {
          table.draw();
        });

        $.fn.dataTable.ext.search.push(function (
          settings,
          searchData,
          index,
          rowData,
          counter
        ) {
          var val = input.val().toLowerCase();

          for (var i = 0, ien = columns.length; i < ien; i++) {
            if (searchData[columns[i]].toLowerCase().indexOf(val) !== -1) {
              return true;
            }
          }

          return false;
        });

        return input;
      }

      $('select#status').on('change', function (e) {
        var status = $(this).val();
        dataTable.columns([8]).search(status).draw();
      });

      $('select#department').on('change', function (e) {
        var department = $(this).val();
        dataTable.columns([6]).search(department).draw();
      });
    });

    document.addEventListener('DOMContentLoaded', function () {
      const declineButtons = document.querySelectorAll('.decline-btn');
      const declineModal = new bootstrap.Modal(document.getElementById('declineConfirmationModal'));
      let currentUserId = null;

      declineButtons.forEach(button => {
        button.addEventListener('click', function () {
          currentUserId = this.getAttribute('data-subject-id');
          declineModal.show();
        });
      });

      document.getElementById('confirmDeclineBtn').addEventListener('click', function () {
        if (currentUserId) {
          fetch('./delete_profiling.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ profiling_id: currentProfilingId }),
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