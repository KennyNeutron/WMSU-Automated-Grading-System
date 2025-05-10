<?php 

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login.php');
}

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
          <button onclick="history.back()" class="bg-none" ><i class='bx bx-chevron-left fs-2 brand-color'></i></button>
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 h1 m-0 text-uppercase">midterm</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="row row-cols-1 row-cols-md-2 d-flex justify-content-between">
          <div class="col">
            <div class="sub-name d-flex align-items-center mb-2">
              <span class="fs-2 h1 m-0 fw-bold brand-color">CS137</span>
              <span class="fs-3 h1 m-0 brand-color">-Software Engineering 1</span>
            </div>
          </div>
          <a href="./subject_setting.php" class="col-2 color-black d-flex align-items-center justify-content-md-end justify-content-start mb-2">
            <span>Settings</span>
            <i class='bx bx-cog fs-3 ms-1 brand-color'></i>
          </a>
        </div>
        
        <div class="row row-cols-1 row-cols-md-2 d-flex justify-content-between">
          <div class="col-3">
            <select type="button" class="btn border dropdown-toggle form-select border-danger mb-4" data-bs-toggle="dropdown">
              <option>1st Semester</option>
              <option>2nd Semester</option>
              <option>Summer</option>
            </select>
          </div>
          <div class="col-3">
            <select type="button" class="btn border dropdown-toggle form-select border-danger mb-4" data-bs-toggle="dropdown">
              <option>Send to</option>
              <option>Lecture</option>
            </select>
          </div>
        </div>

        <div class="content container-fluid mw-100 border rounded shadow p-3">
          
          <div class="search-keyword col-12 flex-lg-grow-0 d-flex">
            <div id="MyButtons" class="d-flex me-4 mb-md-2 mb-lg-0 col-12 col-md-auto"></div>
              <div class="form-group me-2 col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0 ms-lg-auto">
                <select name="student-point_eqv" id="student-point_eqv" class="form-select me-md-2">
                  <option value="">Point Eqv.</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="5">5</option>
                  
                </select>
              </div>
            <div class="input-group">
              <input type="text" name="keyword" id="keyword" placeholder="Search Product" class="form-control">
              <button class="btn btn-outline-secondary brand-bg-color" type="button"><i class='bx bx-search' aria-hidden="true" ></i></button>
            </div>
            <a href="./add_standard.php" class="btn btn-outline-secondary btn-add ms-3 brand-bg-color" type="button"><i class='bx bx-plus-circle'></i></a>
          </div>
          

          <table id="subject-info" class="table table-striped" style="width:250%">
            <thead>
              <tr>
                <th rowspan="5" class="align-middle">#</th>
                <th rowspan="3">Subject ID</th>
                <th rowspan="3">Name</th>
                <th rowspan="3">Email</th>
                <th rowspan="3">Attendance</th>
                <th colspan="18">Standard</th>
                <th rowspan="5">Grade</th>
                <th rowspan="5">Point Equivalent</th>
              </tr>
              <tr>
                <th colspan="5">Quizzes</th>
                <th colspan="5">Activities</th>
                <th colspan="5">Project</th>
                <th colspan="3">Major Exam</th>
              </tr>
              <tr>
                <th>Aug. 9, 2023</th>
                <th>Sep. 10, 2023</th>
                <th>Oct. 14, 2023</th>
                <th rowspan="3" class="align-middle">Ave</th>
                <th rowspan="3" class="align-middle">Weight</th>
          
                <th>Oct. 14, 2023</th>
                <th>Nov. 23, 2023</th>
                <th>Dec. 1, 2023</th>
                <th rowspan="3" class="align-middle">Ave</th>
                <th rowspan="3" class="align-middle">Weight</th>

                <th>Jun. 8, 2023</th>
                <th>Sep. 20, 2023</th>
                <th>Oct. 2, 2023</th>
                <th rowspan="3" class="align-middle">Ave</th>
                <th rowspan="3" class="align-middle">Weight</th>

                <th>dec. 10, 2023</th>
                <th rowspan="3" class="align-middle">Ave</th>
                <th rowspan="3" class="align-middle">Weight</th>
              </tr>
              <tr>
                <th rowspan="2">2021-00000</th>
                <th rowspan="2">Lname, Fname Mi.</th>
                <th rowspan="2">Example@email.com</th>
                <th rowspan="2">3</th>

                <th>Quiz 1</th>
                <th>Quiz 2</th>
                <th>Quiz 3</th>

                <th>Act 1</th>
                <th>Act 2</th>
                <th>Act 3</th>

                <th>Proj 1</th>
                <th>Proj 2</th>
                <th>Proj 3</th>

                <th>major</th>
              </tr>
              <tr>
                <th>10</th>
                <th>20</th>
                <th>30</th>

                <th>40</th>
                <th>20</th>
                <th>50</th>

                <th>20</th>
                <th>20</th>
                <th>20</th>

                <th>100</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td><a class="text-dark" href="./student_record.php">2021-02334</a></td>
                <td>Juan Gardo</td>
                <td>juan@email.com</td>
                <td>2</td>
  
                <td>9</td>
                <td>15</td>
                <td>22</td>
                <td>15.33</td>
                <td>4.6</td>
  
                <td>9</td>
                <td>15</td>
                <td>22</td>
                <td>15.33</td>
                <td>4.6</td>
  
                <td>9</td>
                <td>15</td>
                <td>22</td>
                <td>15.33</td>
                <td>4.6</td>
  
                <td>99</td>
                <td>99.00</td>
                <td>49.9</td>
  
                <td>80</td>
                <td>2.50</td>
              </tr>

              <a href="#">

                <tr>
                  <td>2</td>
                  <td><a href="#">2021-00150</a></td>
                  <td>Franklin Oliveros</td>
                  <td>zxc@email.com</td>
                  <td>1</td>
    
                  <td>10</td>
                  <td>19</td>
                  <td>29</td>
                  <td>19.33</td>
                  <td>5.6</td>
    
                  <td>9</td>
                  <td>15</td>
                  <td>22</td>
                  <td>15.33</td>
                  <td>4.6</td>
    
                  <td>10</td>
                  <td>20</td>
                  <td>30</td>
                  <td>20</td>
                  <td>1</td>
    
                  <td>99</td>
                  <td>99.00</td>
                  <td>49.9</td>
    
                  <td>90</td>
                  <td>1.50</td>
                </tr>
              </a>
            </tbody>
          </table>

        </div>
      </div>

    </main>
  </div>

  <?php
    require_once('./includes/js.php');
  ?>
  <script src="./js/subject_info-table.js"></script>
  
</body>
</html>