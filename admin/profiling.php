<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login');
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Profiling';
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
            <span class="fs-2 fw-bold h1 m-0 brand-color">PROFILING</span>
          </div>
        </div>
      </div>

      <div class="m-4">

        <div class="content container-fluid mw-100 border rounded shadow p-3">
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
          <div class="search-keyword col-12 flex-lg-grow-0 d-flex justify-content-between">
            <div class="input_width d-flex gap-2">
              <div class="form-group col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0">
                <select name="department" id="department" class="form-select me-md-2">
                  <option value="">Department</option>
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

              <div class="form-group col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0 ms-lg-auto">
                <select name="acad_type" id="acad_type" class="form-select me-md-2">
                  <option value="" disabled selected>Academic Rank</option>
                  <option value="Adjunct Faculty">Adjunct Faculty</option>
                  <option value="Instructor">Instructor</option>
                  <option value="Professor">Professor</option>
                </select>
              </div>

              <div class="form-group col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0">
                <select name="faculty_type" id="faculty_type" class="form-select me-md-2">
                  <option value="" disabled selected>Faculty Type</option>
                  <option value="Regular Lecturer">Regular Lecturer</option>
                  <option value="Visiting Lecturer">Visiting Lecturer</option>
                </select>
              </div>
            </div>
            <div class="input_width d-flex" style="width: 40% !important;">
              <div class="input-group">
                <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control">
                <button class="btn btn-outline-secondary brand-bg-color" type="button"><i class='bx bx-search'
                    aria-hidden="true"></i></button>
              </div>
              <a href="./add_faculty.php<?= isset($_GET['department_id']) ? '?department_id=' . htmlspecialchars($_GET['department_id']) : '' ?>"
                class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i
                  class='bx bx-plus-circle'></i></a>
            </div>
          </div>

          <?php
          require_once '../classes/profiling.class.php';
          require_once '../tools/functions.php';

          $profiling = new Profiling();

          $department_id = $_GET['department_id'] ?? '';
          $profiling_array = $profiling->show($department_id);
          $counter = 1;
          ?>
          <hr>
          <table id="main_profiling" class="table table-striped" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Academic Rank</th>
                <th>Designation</th>
                <th <?= isset($_GET['department_id']) ? 'style="display: none;"' : '' ?>>Department</th>
                <th>Faculty Type</th>
                <th>Start Service</th>
                <th>End Service</th>
                <th width="5%">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $counter = 1;
              foreach ($profiling_array as $item) {
                ?>
                <tr>
                  <td><?= $counter ?></td>
                  <td><?= $item['emp_id'] ?></td>
                  <td><?= $item['l_name'] ?>, <?= $item['f_name'] ?>
                    <?= !empty($item['m_name']) ? substr($item['m_name'], 0, 1) . '.' : '' ?>
                  </td>
                  <td><?= $item['email'] ?></td>
                  <td><?= $item['acad_type'] ?></td>
                  <td><?= $item['designation'] ?></td>
                  <td <?= isset($_GET['department_id']) ? 'style="display: none;"' : '' ?>><?= $item['department_name'] ?>
                  </td>
                  <td><?= $item['faculty_type'] ?></td>
                  <td><?= $item['start_service'] ?></td>
                  <td><?= $item['end_service'] ?></td>
                  <td class="text-center">
                    <?php
                    if (isset($_GET['department_id'])) {
                      $department_id = $_GET['department_id'];
                      echo "<a href='./edit_faculty?department_id=$department_id&profiling_id=" . $item['profiling_id'] . "'><i class='bx bx-edit text-success fs-4'></i></a>";
                    } else {
                      echo "<a href='./edit_faculty?profiling_id=" . $item['profiling_id'] . "'><i class='bx bx-edit text-success fs-4'></i></a>";
                    }
                    ?>
                    <button class="delete-btn bg-none" data-subject-id="<?= $item['profiling_id'] ?>">
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

    </main>
  </div>

  <!-- confirm delete modal markup -->
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
          Are you sure you want to delete this Faculty?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
        </div>
      </div>
    </div>
  </div>

  <script src="./js/main.js"></script>
  <script src="./js/profiling_table.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const deleteButtons = document.querySelectorAll('.delete-btn');
      const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
      let currentProfilingId = null;

      deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
          currentProfilingId = this.getAttribute('data-subject-id');
          deleteModal.show();
        });
      });

      document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (currentProfilingId) {
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