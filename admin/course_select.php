<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login');
}

require_once '../classes/course_select.class.php';
require_once '../classes/curr_year.class.php';

$curr_year = new Curr_year();
$curr_year_id = $_GET['curr_year_id'] ?? '';

$yearRange = $curr_year->getYearRangeById($curr_year_id);

$current_year = date('Y');
$is_previous_year = $yearRange['year_end'] < $current_year;

if ($yearRange) {
  $head = "CURRICULUM {$yearRange['year_start']}-{$yearRange['year_end']}";
} else {
  echo "Invalid Curriculum Year";
}

$coursecurr = new Course_curr();
$degree_levels = $coursecurr->getUniqueDegreeLevels();
$selected_degree = $_GET['degree_lvl'] ?? '';
$selected_degree = urldecode($selected_degree);
$selected_degree = htmlspecialchars_decode($selected_degree, ENT_QUOTES);
$keyword = $_GET['keyword'] ?? '';
$is_search = !empty($keyword);

// If search is active, ignore degree level filters
if ($is_search) {
  $course_currArray = $coursecurr->searchByCourseName($keyword);
} else {
  if (!empty($selected_degree)) {
    $course_currArray = $coursecurr->filterByDegreeLevel($selected_degree);
  } else {
    $course_currArray = $coursecurr->show();
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<?php
$title = $head;
$curriculum_page = 'active';
include '../includes/admin_head.php';
?>

<body>
  <div class="home">
    <div class="side">
      <?php
      require_once('../includes/admin_sidepanel.php');
      ?>
    </div>
    <main>
      <div class="header">
        <?php
        require_once('../includes/admin_header.php');
        ?>
      </div>

      <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
        <div class="d-flex align-items-center">
          <a href="./index" class="bg-none"><i class='bx bx-chevron-left fs-2 brand-color'></i></a>
          <div class="container-fluid d-flex justify-content-center">
            <span class='fs-2 fw-bold h1 m-0 brand-color'>
              <?= $head ?>
            </span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="search-keyword col-12 flex-lg-grow-0 d-flex justify-content-between mb-4">
          <div class="input_width d-flex gap-2">
            <form id="filterForm" method="GET" action="">
              <div class="input-group">
                <select name="degree_lvl" id="degree_lvl" class="form-select" onchange="autoFilter()">
                  <option value="">All Degree Levels</option>
                  <?php foreach ($degree_levels as $level): ?>
                    <option value="<?= urlencode(htmlspecialchars($level, ENT_QUOTES)) ?>" <?= ($selected_degree === $level) ? 'selected' : '' ?>>
                      <?= $level ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </form>
          </div>

          <div class="input_width d-flex" style="width: 40% !important;">
            <form id="searchForm" method="GET" action="" style="width: 100% !important;">
              <div class="input-group">
                <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control"
                  value="<?= htmlspecialchars($keyword) ?>">
                <button class="btn btn-outline-secondary brand-bg-color" type="submit"><i class='bx bx-search'
                    aria-hidden="true"></i></button>
              </div>
            </form>

            <?php if (!$is_previous_year): ?>
              <a href="./add_program?curr_year_id=<?= $_GET['curr_year_id'] ?>"
                class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i
                  class='bx bx-plus-circle'></i></a>
            <?php endif; ?>
          </div>

        </div>

        <?php if ($is_search): ?>
          <!-- Display search results grouped by degree level -->
          <?php
          // Extract unique degree levels from search results
          $unique_degree_levels = array_unique(array_column($course_currArray, 'degree_level'));
          ?>
          <?php if (!empty($unique_degree_levels)): ?>
            <div class="row">
              <?php foreach ($unique_degree_levels as $level): ?>
                <?php
                // Filter courses for the current degree level
                $filtered_courses = array_filter($course_currArray, fn($item) => $item['degree_level'] === $level);
                ?>
                <?php if (!empty($filtered_courses)): ?>
                  <!-- Degree Level Title -->
                  <div class="col-12 mb-1 mt-4">
                    <h4><?= $level ?></h4>
                    <hr>
                  </div>

                  <!-- List of Courses -->
                  <div class="col-12 mb-5">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                      <?php foreach ($filtered_courses as $item): ?>
                        <div class="col">
                          <div
                            class="course d-flex align-items-center justify-content-center brand-bg-color p-4 fs-4 h-100 rounded position-relative">
                            <a href="./course_time_select?curr_year_id=<?= $_GET['curr_year_id'] . '&course_id=' . $item['college_course_id'] ?>"
                              class="stretched-link"></a>

                            <!-- Dropdown for actions -->
                            <div class="dropdown-container position-absolute">
                              <i class='bx bx-dots-vertical-rounded fs-5 text-white position-absolute top-0'
                                id="dropdownMenuButton" style="cursor: pointer;"></i>
                              <div class="dropdown-menu">
                                <a href="./edit_course?college_course_id=<?= $item['college_course_id'] ?>&curr_year_id=<?= $_GET['curr_year_id'] ?>"
                                  class="dropdown-item">
                                  <i class='bx bx-edit text-success fs-6'></i> Edit
                                </a>
                                <button class="delete-btn dropdown-item" data-subject-id="<?= $item['college_course_id'] ?>">
                                  <i class='bx bx-trash-alt text-danger fs-6'></i> Delete
                                </button>
                              </div>
                            </div>

                            <span><?= htmlspecialchars($item['name']) ?></span>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-center">No courses found.</p>
          <?php endif; ?>
        <?php else: ?>
          <!-- Display filtered results grouped by degree level -->
          <?php if ($course_currArray): ?>
            <div class="row">
              <?php foreach ($degree_levels as $level): ?>
                <?php
                $filtered_courses = array_filter($course_currArray, fn($item) => $item['degree_level'] === $level);
                ?>
                <?php if (!empty($filtered_courses)): ?>
                  <div class="col-12 mb-1 mt-4">
                    <h4><?= $level ?></h4>
                    <hr>
                  </div>
                  <div class="col-12 mb-5">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                      <?php foreach ($filtered_courses as $item): ?>
                        <div class="col">
                          <div
                          
                            class="course d-flex align-items-center justify-content-center brand-bg-color p-4 fs-4 h-100 rounded position-relative">
                            <a 
                            
                            href="./curri_page?year_id=<?= $_GET['curr_year_id'] ?>&course_id=<?= $item['college_course_id'] ?>&time_id=1&year_level_id=1&semester_id=1"

                              class="stretched-link"></a>

                             
                            <!-- Dropdown for actions -->
                            <div class="dropdown-container position-absolute">
                              <i class='bx bx-dots-vertical-rounded fs-5 text-white position-absolute top-0'
                                id="dropdownMenuButton" style="cursor: pointer;"></i>
                              <div class="dropdown-menu">
                                <a href="./edit_course?college_course_id=<?= $item['college_course_id'] ?>&curr_year_id=<?= $_GET['curr_year_id'] ?>"
                                  class="dropdown-item">
                                  <i class='bx bx-edit text-success fs-6'></i> Edit
                                </a>
                                <button class="delete-btn dropdown-item" data-subject-id="<?= $item['college_course_id'] ?>">
                                  <i class='bx bx-trash-alt text-danger fs-6'></i> Delete
                                </button>
                              </div>
                            </div>

                            <span><?= htmlspecialchars($item['name']) ?></span>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-center">No courses found.</p>
          <?php endif; ?>
        <?php endif; ?>


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
              Are you sure you want to delete this Curriculum?
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
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const dropdownContainers = document.querySelectorAll('.dropdown-container');

      dropdownContainers.forEach(container => {
        const menu = container.querySelector('.dropdown-menu');
        const trigger = container.querySelector('.bx-dots-vertical-rounded');

        trigger.addEventListener('click', event => {
          event.stopPropagation();
          const isVisible = menu.style.display === 'block';
          document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
            otherMenu.style.display = 'none';
          });
          menu.style.display = isVisible ? 'none' : 'block';
        });
      });

      document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
          menu.style.display = 'none';
        });
      });
    });

    function autoFilter() {
      const degreeLevel = document.getElementById('degree_lvl').value;
      const urlParams = new URLSearchParams(window.location.search);
      urlParams.set('degree_lvl', degreeLevel);
      urlParams.delete('keyword');
      window.location.search = urlParams.toString();
    }

    document.addEventListener("DOMContentLoaded", () => {
      const searchForm = document.getElementById("searchForm");
      const keywordInput = document.getElementById("keyword");

      // Add an event listener to the search button
      searchForm.addEventListener("submit", (event) => {
        event.preventDefault(); // Prevent the default form submission

        const keyword = keywordInput.value.trim(); // Get the trimmed keyword
        const urlParams = new URLSearchParams(window.location.search);

        if (keyword) {
          urlParams.set("keyword", keyword); // Add the keyword to the URL
        } else {
          urlParams.delete("keyword"); // Remove the keyword if empty
        }

        // Redirect the page to include the updated query string
        window.location.search = urlParams.toString();
      });
    });

    $(document).ready(function () {
      $('.delete-btn').on('click', function () {
        var college_course_id = $(this).data('subject-id');
        $('#confirmDeleteBtn').data('college_course-id', college_course_id);
        $('#deleteConfirmationModal').modal('show');
      });

      $('#confirmDeleteBtn').on('click', function () {
        var college_course_id = $(this).data('college_course-id');

        $.ajax({
          url: './delete_course.php',
          method: 'POST',
          data: {
            college_course_id: college_course_id
          },
          success:
            function (response) {
              showAlert('Program deleted successfully!', 'success');
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

        setTimeout(() => {
          alertContainer.innerHTML = '';
        }, 1000);
      }
    });
  </script>
</body>

</html>