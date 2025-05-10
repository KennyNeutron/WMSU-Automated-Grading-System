<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login.php');
  exit(); // Make sure to exit after redirection
}
require_once './classes/faculty_subs.class.php';
require_once './classes/period.class.php';
require_once './classes/component.class.php';

$selected_faculty_sub_id = isset($_POST['faculty_sub_id']) ? $_POST['faculty_sub_id'] : null;

$fac_subs = new Faculty_Subjects();
$period = new Periods();
$components = new SubjectComponents();

$all_subs = $fac_subs->getByUser($_SESSION['emp_id']);
$subject = $fac_subs->getProf($selected_faculty_sub_id);

$all_periods = $period->showPeriodBySub($selected_faculty_sub_id);
$midtermPeriod = $period->getIdMidterm($selected_faculty_sub_id);
$finaltermPeriod = $period->getIdFinalterm($selected_faculty_sub_id);
$midtermComp = $period->showMidterm($selected_faculty_sub_id);
$finaltermComp = $period->showFinalterm($selected_faculty_sub_id);
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
$title = 'Subject Settings';
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
          <a href="./select_subject_setting" class="bg-none">
            <i class='bx bx-chevron-left fs-2 brand-color'></i>
          </a>

          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">Subject Settings</span>
          </div>
        </div>
      </div>



      <div class="d-flex justify-content-end mt-4 me-4">
        <a href="./edit_pointeqv?faculty_sub_id=<?= $selected_faculty_sub_id ?>&period=midterm&period_id=<?= $midtermPeriod ?>"
          class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"> Edit Grade Equivalent</a>
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
          <div class="content brand-border-color mw-100 rounded shadow py-3">
            <nav>
              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <?php
                // Loop through all periods and generate tabs
                foreach ($all_periods as $index => $period):
                  $period_name = str_replace(' ', '', $period['period_type']);
                  $tab_id = "nav-{$period_name}-tab";
                  $content_id = "nav-{$period_name}";
                  $active_class = ($index === 0) ? 'active' : ''; // Set the first tab as active
                  ?>
                  <button class="nav-link text-dark <?= $active_class ?>" id="<?= $tab_id ?>" data-bs-toggle="tab"
                    data-bs-target="#<?= $content_id ?>" type="button" role="tab" aria-controls="<?= $content_id ?>"
                    aria-selected="<?= ($index === 0) ? 'true' : 'false' ?>">
                    <?= $period['period_type'] ?>
                  </button>
                  <?php
                endforeach;
                ?>
              </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">

              <div class="tab-pane fade show active" id="nav-Midterm" role="tabpanel" aria-labelledby="nav-Midterm-tab">
                <div class="d-flex flex-column align-items-center">
                  <h3 class="brand-color"><?= $subject ? ucwords($subject['sub_name']) : '' ?></h3>
                  <h4><?= $subject ? $subject['sub_code'] . $sub_type : "" ?></h4>
                  <h4 style="margin: 0; padding: 0;">(<?= $subject ? $subject['yr_sec'] : "" ?>)</h4>
                </div>

                <div class="row m-2 row-cols-1 row-cols-md-2 d-flex justify-content-end">
                  <!-- <form class="d-flex justify-content-start align-items-center">
                    <label class="me-2">Number of absences:</label>
                    <input class="border-bottom px-2" id="disabledTextInput" type="number" title="3" value="7" disabled>
                    <i class='bx bx-edit ms-2' id="edit_att_req"></i>
                  </form> -->
                  <?php if (!empty($midtermPeriod)): ?>
                    <div class="col-3 d-flex justify-content-end">
                      <a href="./add_criteria?faculty_sub_id=<?= $selected_faculty_sub_id ?>&period=midterm&period_id=<?= $midtermPeriod ?>"
                        class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i
                          class='bx bx-plus-circle'></i></a>
                    </div>
                  <?php endif; ?>
                </div>

                <table id="subject_setting1" class="table table-striped cell-border" style="width:100%">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Criteria</th>
                      <th scope="col">Weight</th>
                      <th scope="col" width="5%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $counter = 1;
                    $totalWeight = 0;
                    foreach ($midtermComp as $item):
                      ?>
                      <tr>
                        <td><?= $counter ?></td>
                        <td><?= $item['component_type'] ?></td>
                        <td class="midterm-weight"><?= $item['weight'] ?>%</td>
                        <td class="text-center">
                          <a
                            href="./edit_criteria.php?faculty_sub_id=<?= $selected_faculty_sub_id ?>&component_id=<?= $item['component_id'] ?>&period=midterm">
                            <i class='bx bx-edit text-success fs-4'></i>
                          </a>
                          <button class="delete-btn bg-none" data-subject-id="<?= $item['component_id'] ?>">
                            <i class='bx bx-trash-alt text-danger fs-4'></i>
                          </button>
                        </td>
                      </tr>
                      <?php
                      $totalWeight += $item['weight'];
                      $counter++;
                    endforeach;
                    ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2" class="fw-bold text-end">Total</td>
                      <td id="midterm-total-weight" class="fw-bold"><?= $totalWeight ?>%</td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>

              </div>

              <div class="tab-pane fade" id="nav-FinalTerm" role="tabpanel" aria-labelledby="nav-FinalTerm-tab">
                <div class="tab-pane fade show active" id="nav-Midterm" role="tabpanel" aria-labelledby="nav-Midterm-tab">
                  <div class="d-flex flex-column align-items-center">
                    <h3 class="brand-color"><?= $subject ? ucwords($subject['sub_name']) : '' ?></h3>
                    <h4><?= $subject ? $subject['sub_code'] . $sub_type : "" ?></h4>
                    <h4 style="margin: 0; padding: 0;">(<?= $subject ? $subject['yr_sec'] : "" ?>)</h4>
                  </div>

                  <div class="row m-2 row-cols-1 row-cols-md-2 d-flex justify-content-end">
                    <!-- <form class="d-flex justify-content-start align-items-center">
                      <label class="me-2">Number of absences:</label>
                      <input class="border-bottom px-2" id="disabledTextInput" type="number" title="3" value="7" disabled>
                      <i class='bx bx-edit ms-2' id="edit_att_req"></i>
                    </form> -->
                    <div class="col-3 d-flex justify-content-end">
                      <a href="./add_criteria?faculty_sub_id=<?= $selected_faculty_sub_id ?>&period=finalterm&period_id=<?= $finaltermPeriod ?>"
                        class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i
                          class='bx bx-plus-circle'></i></a>
                    </div>
                  </div>

                  <table id="subject_setting2" class="table table-striped cell-border" style="width:100%">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Criteria</th>
                        <th scope="col">Weight</th>
                        <th scope="col" width="5%">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $counter = 1;
                      $totalWeight = 0;
                      foreach ($finaltermComp as $item):
                        ?>
                        <tr>
                          <td><?= $counter ?></td>
                          <td><?= $item['component_type'] ?></td>
                          <td class="finalterm-weight"><?= $item['weight'] ?>%</td>
                          <td class="text-center">
                            <a
                              href="./edit_criteria.php?faculty_sub_id=<?= $selected_faculty_sub_id ?>&component_id=<?= $item['component_id'] ?>&period=finalterm">
                              <i class='bx bx-edit text-success fs-4'></i>
                            </a>
                            <button class="delete-btn bg-none" data-subject-id="<?= $item['component_id'] ?>">
                              <i class='bx bx-trash-alt text-danger fs-4'></i>
                            </button>
                          </td>
                        </tr>
                        <?php
                        $totalWeight += $item['weight'];
                        $counter++;
                      endforeach;
                      ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="2" class="fw-bold text-end">Total</td>
                        <td id="finalterm-total-weight" class="fw-bold"><?= $totalWeight ?>%</td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <?php
        endif;
        ?>
    </main>
  </div>

  <!-- Confirmation Modal -->
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
          <p>Are you sure you want to delete this criteria?</p>
          <p class="text-danger"><strong>Warning:</strong> This will also delete all exisiting data/grades associated
            with this criteria.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
        </div>
      </div>
    </div>
  </div>

  <?php require_once('./includes/js.php'); ?>
  <script src="./js/subject_setting-table.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
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

      $(document).ready(function () {
        $('.delete-btn').on('click', function () {
          var component_id = $(this).data('subject-id');
          $('#confirmDeleteBtn').data('component-id', component_id);
          $('#deleteConfirmationModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function () {
          var component_id = $(this).data('component-id');

          $.ajax({
            url: './delete_criteria.php',
            method: 'POST',
            data: {
              component_id: component_id
            },
            success:
              function (response) {
                showAlert('Criteria deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
              },
            error:
              function (xhr, status, error) {
                console.error(xhr.responseText);
                alert('Error occurred: ' + error);
              }
          });
        });

        function showAlert(message, type) {
          const alertContainer = document.getElementById('alertContainer');
          const alertHTML = `
            <div class="alert alert-${type} d-flex flex-row align-items-center gap-2 position-fixed top-0 start-50 translate-middle-x w-auto mt-4 z-index-1050" role="alert">
               <i class='bx bx-check-circle'></i> ${message}
            </div>
        `;
          alertContainer.innerHTML = alertHTML;

          // setTimeout(() => {
          //     alertContainer.innerHTML = '';
          // }, 1000);
        }
      });

      function updateTotalWeightColor(id) {
        const totalWeightElement = document.getElementById(id);
        if (totalWeightElement) {
          let totalWeight = parseInt(totalWeightElement.innerText.replace('%', ''), 10);
          if (totalWeight === 100) {
            totalWeightElement.style.color = 'green'; // Success
          } else if (totalWeight < 100) {
            totalWeightElement.style.color = 'darkgoldenrod'; // Below 100
          } else {
            totalWeightElement.style.color = 'red'; // Above 100
          }
        }
      }

      updateTotalWeightColor('midterm-total-weight');
      updateTotalWeightColor('finalterm-total-weight');
    });
  </script>
</body>

</html>