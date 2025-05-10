<?php 

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<?php 
	$title = 'Grade Posted';
  $grade_page = 'active';
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
            <span class="fs-2 h1 m-0">Grade Posted</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="row row-cols-1 row-cols-md-3 g-3">
          <div class="col">            
            <a href="#">
              <div class="brand-bg-color p-4 fs-3 rounded">
                <span>2018-2019</span>
              </div>
            </a>
          </div>
          <div class="col">            
            <a href="#">
              <div class="brand-bg-color p-4 fs-3 rounded">
                <span>2019-2020</span>
              </div>
            </a>
          </div>
          <div class="col">            
            <a href="#">
              <div class="brand-bg-color p-4 fs-3 rounded">
                <span>2020-2021</span>
              </div>
            </a>
          </div>
          <div class="col">            
            <a href="#">
              <div class="brand-bg-color p-4 fs-3 rounded">
                <span>2021-2022</span>
              </div>
            </a>
          </div>
          <div class="col">            
            <a href="./grade_sub-select.php">
              <div class="brand-bg-color p-4 fs-3 rounded">
                <span>2022-2023</span>
              </div>
            </a>
          </div>
        </div>
      </div>

    </main>
  </div>

  <script src="./js/main.js"></script>
  <script src="./js/student_table.js"></script>
  
</body>
</html>