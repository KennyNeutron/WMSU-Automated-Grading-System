<?php 

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<?php 
	$title = 'Subject Setting';
  $sub_setting_page = 'active';
	include './includes/head.php';
?>
<body>
  <div class="home">
    <div class="side">
      <?php
        require_once('./includes/sidepanel.php')
      ?> 
    </div>
    <main>
      <div class="header" >
      <?php
        require_once('./includes/header.php')
      ?>
      </div>
      
      <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;" >
        <div class="d-flex align-items-center">
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 h1 m-0">Subject Setting</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-4">
          <div class="col">
            <select type="button" class="btn border dropdown-toggle form-select border-danger mb-4" data-bs-toggle="dropdown">
              <option>Apply to all</option>
              <option>CS140</option>
              <option>CS137</option>
            </select>
          </div>
        </div>
        
        <div class="content brand-border-color mw-100 rounded shadow py-3">
          <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <button class="nav-link text-dark active" id="nav-midterm-tab" data-bs-toggle="tab" data-bs-target="#nav-midterm" type="button" role="tab" aria-controls="nav-midterm" aria-selected="true">Midterm</button>
              <button class="nav-link text-dark" id="nav-finalterm-tab" data-bs-toggle="tab" data-bs-target="#nav-finalterm" type="button" role="tab" aria-controls="nav-finalterm" aria-selected="false">Final Term</button>
            </div>
          </nav>
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-midterm" role="tabpanel" aria-labelledby="nav-midterm-tab">
              <div class="row m-2 row-cols-1 row-cols-md-2 d-flex justify-content-between">
                <form class="d-flex justify-content-start align-items-center">
                  <label class="me-2">Number of absences:</label>
                  <input class="border-bottom px-2" id="disabledTextInput" type="number" title="3" value="7" disabled>
                  <i class='bx bx-edit ms-2' id="edit_att_req"></i>
                </form>
                <div class="col-3 d-flex justify-content-end">
                  <a href="./add_standard-setting.php" class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i class='bx bx-plus-circle'></i></a>
                </div>
              </div>

              <div class="d-flex justify-content-end">
              </div>

              <table id="subject_setting" class="table table-striped cell-border" style="width:100%">
                <thead>
                  <tr>
                    <th>Criteria</th>
                    <th>Weight</th>
                    <th width="5%">action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Attendance</td>
                    <td>5% </td>
                    <td class="text-center">
                      <a href="# "><i class='bx bx-edit text-success' ></i></a>
                      <i class='bx bx-trash-alt text-danger' ></i>
                    </td>
                  </tr>
                  <tr>
                    <td>Quiz</td>
                    <td>30% </td>
                    <td class="text-center">
                      <a href="# "><i class='bx bx-edit text-success' ></i></a>
                      <i class='bx bx-trash-alt text-danger' ></i>
                    </td>
                  </tr>
                  <tr>
                    <td>Activity</td>
                    <td>15% </td>
                    <td class="text-center">
                      <a href="# "><i class='bx bx-edit text-success' ></i></a>
                      <i class='bx bx-trash-alt text-danger' ></i>
                    </td>
                  </tr>
                  <tr>
                    <td>Major Exam</td>
                    <td>50% </td>
                    <td class="text-center">
                      <a href="# "><i class='bx bx-edit text-success' ></i></a>
                      <i class='bx bx-trash-alt text-danger' ></i>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="tab-pane fade" id="nav-finalterm" role="tabpanel" aria-labelledby="nav-finalterm-tab">
            <div class="tab-pane fade show active" id="nav-midterm" role="tabpanel" aria-labelledby="nav-midterm-tab">
              <div class="row m-2 row-cols-1 row-cols-md-2 d-flex justify-content-between">
                <form class="d-flex justify-content-start align-items-center">
                  <label class="me-2">Number of absences:</label>
                  <input class="border-bottom px-2" id="disabledTextInput2" type="number" title="3" disabled>
                  <i class='bx bx-edit ms-2' id="edit_att_req2"></i>
                </form>
                <div class="col-3 d-flex justify-content-end">
                  <a href="./add_standard-setting.php" class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i class='bx bx-plus-circle'></i></a>
                </div>
              </div>

              <div class="d-flex justify-content-end">
              </div>

              <table id="subject_setting" class="table table-striped cell-border" style="width:100%">
                <thead>
                  <tr>
                    <th>Criteria</th>
                    <th>Weight</th>
                    <th width="5%">action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Attendance</td>
                    <td>10% </td>
                    <td class="text-center">
                      <a href="# "><i class='bx bx-edit text-success' ></i></a>
                      <i class='bx bx-trash-alt text-danger' ></i>
                    </td>
                  </tr>
                  <tr>
                    <td>Quiz</td>
                    <td>20% </td>
                    <td class="text-center">
                      <a href="# "><i class='bx bx-edit text-success' ></i></a>
                      <i class='bx bx-trash-alt text-danger' ></i>
                    </td>
                  </tr>
                  <tr>
                    <td>Activity</td>
                    <td>20% </td>
                    <td class="text-center">
                      <a href="# "><i class='bx bx-edit text-success' ></i></a>
                      <i class='bx bx-trash-alt text-danger' ></i>
                    </td>
                  </tr>
                  <tr>
                    <td>Major Exam</td>
                    <td>50% </td>
                    <td class="text-center">
                      <a href="# "><i class='bx bx-edit text-success' ></i></a>
                      <i class='bx bx-trash-alt text-danger' ></i>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>

  <?php
    require_once('./includes/js.php');
  ?>
  <script src="./js/subject_setting-table.js"></script>
  
</body>
</html>