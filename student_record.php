<?php 

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<?php 
	$title = 'Student list';
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
            <span class="fs-2 h1 m-0">Student Record</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="row row-cols-1 row-cols-md-2 mx-2 border-bottom py-2">
          <div class="col-md-3 fw-bolder fs-3">
            Information
          </div>
          <div class="col-md-9">
            <span>School ID:</span>
            <p>2021-02334</p>
            <span>Name:</span>
            <p>Juan Gardo</p>
            <span>Email Address:</span>
            <p>juan@email.com</p>
          </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 mx-2 border-bottom py-2">
          <div class="col-md-3 fw-bolder fs-3">
            Attendance
          </div>
          <div class="col-md-9">
            <span>Number of absences:</span>
            <p>2</p>
          </div>
        </div>
        
        <div class="row row-cols-1 row-cols-md-2 mx-2 border-bottom py-2">
          <div class="col-md-3 fw-bolder fs-3">
            Quiz
          </div>
          <div class="col-md-9">
            <div class="row row-cols-1 row-cols-md-2">
              <div class="col">
                <span class="fw-bold" >Quiz no.1</span><br>
                <span>Date:</span>
                <p>2023, Aug. 9</p>
                <span>Score:</span>
                <p>9 <span>/</span> 10</p>
              </div>

              <div class="col">
                <span class="fw-bold" >Quiz no.2</span><br>
                <span>Date:</span>
                <p>2023, Sep. 10</p>
                <span>Score:</span>
                <p>15 <span>/</span> 20</p>
              </div>

              <div class="col">
                <span class="fw-bold" >Quiz no.3</span><br>
                <span>Date:</span>
                <p>2023, Oct. 14</p>
                <span>Score:</span>
                <p>22 <span>/</span> 30</p>
              </div>
            </div>
          </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 mx-2 border-bottom py-2">
          <div class="col-md-3 fw-bolder fs-3">
            Activities
          </div>
          <div class="col-md-9">
            <div class="row row-cols-1 row-cols-md-2">
              <div class="col">
                <span class="fw-bold" >Activity no.1</span><br>
                <span>Date:</span>
                <p>2023, Oct. 13</p>
                <span>Score:</span>
                <p>9 <span>/</span> 40</p>
              </div>

              <div class="col">
                <span class="fw-bold" >Activity no.2</span><br>
                <span>Date:</span>
                <p>2023, Nov. 23</p>
                <span>Score:</span>
                <p>15 <span>/</span> 20</p>
              </div>

              <div class="col">
                <span class="fw-bold" >Activity no.3</span><br>
                <span>Date:</span>
                <p>2023, Dec. 1</p>
                <span>Score:</span>
                <p>22 <span>/</span> 50</p>
              </div>
            </div>
          </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 mx-2 border-bottom py-2">
          <div class="col-md-3 fw-bolder fs-3">
            Major Exam
          </div>
          <div class="col-md-9">
            <div class="row row-cols-1 row-cols-md-1">
              <div class="col">
                <span class="fw-bold" >Activity no.1</span><br>
                <span>Date:</span>
                <p>2023, Dec. 10</p>
                <span>Score:</span>
                <p>99 <span>/</span> 100</p>
              </div>
            </div>
          </div>
        </div>

      </div>

    </main>
  </div>

  <?php
    require_once('./includes/js.php');
  ?>
  <script src="./js/student_table.js"></script>
  
</body>
</html>