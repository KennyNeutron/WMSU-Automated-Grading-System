<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login');
}

require_once '../classes/curr_year.class.php';

$currYear = new Curr_year();
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
  $keyword = $_GET['keyword'];
  $results = $currYear->searchByYearStart($keyword);
  if (empty($results)) {
    echo "<script> alert('No Curriculum found'); window.location.href='./index.php'; </script>";
    exit;
  }
} else {
  $curr_yearArray = $currYear->show();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Curriculum';
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
      <div class="header">
        <?php
        require_once('../includes/admin_header.php');
        ?>
      </div>

      <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
        <div class="d-flex align-items-center">
          <div class="container-fluid d-flex justify-content-center">
            <span class='fs-2 fw-bold h1 m-0 brand-color'>
              Curriculum
            </span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="search-keyword col-12 flex-lg-grow-0 d-flex mb-3">
          <form class="input-group">
            <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control">
            <button class="btn btn-outline-secondary brand-bg-color" type="button" name="keyword"
              onclick="searchYearStart()"><i class='bx bx-search' aria-hidden="true"></i></button>
          </form>

          <a href="./add_curri" class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i
              class='bx bx-plus-circle'></i></a>
        </div>

        <div class="curriculum row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
          <?php
          require_once '../classes/curr_year.class.php';
          $curr_year = new Curr_year();
          $curr_yearArray = $curr_year->show();

          if ($curr_yearArray) {
            foreach ($curr_yearArray as $item) {
              // Check if keyword is set and if the item matches the keyword
              $displayItem = true;
              if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
                $keyword = strtolower($_GET['keyword']);
                if (strtolower($item['year_start']) != $keyword) {
                  $displayItem = false;
                }
              }

              if ($displayItem) {
                ?>

                <div class="col">
                  <div
                    class="d-flex align-items-center justify-content-between brand-bg-color p-4 fs-4 rounded position-relative">
                    <a href="./course_select?curr_year_id=<?= $item['curr_year_id'] ?>" class="stretched-link"></a>
                    <i class='bx bxs-folder-open text-white opacity-50'></i>
                    <div class="dropdown-container">
                      <i class='bx bx-dots-vertical-rounded fs-3 text-white position-absolute top-0'
                        id="dropdownMenuButton"></i>
                      <div class="dropdown-menu">

                        <a href="./edit_curri?curr_year_id=<?= $item['curr_year_id'] ?>" class="dropdown-item">
                          <i class='bx bx-edit text-success fs-6'></i> Edit
                        </a>

                        <button class="delete-btn dropdown-item" data-subject-id="<?= $item['curr_year_id'] ?>">
                          <i class='bx bx-trash-alt text-danger fs-6'></i> Delete
                        </button>

                      </div>
                    </div>
                    <div class="d-flex flex-column justify-content-start me-3">
                      <span>Curriculum</span>
                      <span><?= $item['year_start'] ?> - <?= $item['year_end'] ?></span>
                    </div>
                  </div>
                </div>

                <?php
              }
            }
          }
          ?>

        </div>
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
              <h6>Are you sure you want to delete this Curriculum?</h6>
              <span class="text-danger">(This action will also delete existing data in the curriculum)</span>
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
    function searchYearStart() {
      var keyword = document.getElementById('keyword').value.trim();
      if (keyword !== '') {
        fetch('search.php?keyword=' + encodeURIComponent(keyword))
          .then(response => {
            if (response.ok) {
              return response.text();
            }
            throw new Error('Network response was not ok.');
          })
          .then(data => {
            if (data === 'none') {
              document.getElementById('searchResult').textContent = 'No matching items found.';
            } else {
              document.getElementById('searchResult').textContent = data;
            }
          })
          .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
          });
      }
    }
    
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

    $(document).ready(function () {
      $('.delete-btn').on('click', function () {
        var curr_year_Id = $(this).data('subject-id');
        $('#confirmDeleteBtn').data('curr_year-id', curr_year_Id);
        $('#deleteConfirmationModal').modal('show');
      });

      $('#confirmDeleteBtn').on('click', function () {
        var curr_year_Id = $(this).data('curr_year-id');

        $.ajax({
          url: './delete_curr.php',
          method: 'POST',
          data: {
            curr_year_id: curr_year_Id
          },
          success:
            function (response) {
              showAlert('Curriculum deleted successfully!', 'success');
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