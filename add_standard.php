<?php 

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<?php 
	$title = 'Pick Standard';
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
            <span class="fs-2 h1 m-0">Standard</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="row row-cols-1 row-cols-md-2 g-3">
          <div class="col">
            <a href="./add_standard-form.php">
              <div class="brand-bg-color p-4 fs-3 rounded">
                <span>Quiz</span>
              </div>
            </a>
          </div>
          <div class="col">            
            <a href="#">
              <div class="brand-bg-color p-4 fs-3 rounded">
                <span>Activity</span>
              </div>
            </a>
          </div>
          <div class="col">            
            <a href="#">
              <div class="brand-bg-color p-4 fs-3 rounded">
                <span>Project</span>
              </div>
            </a>
          </div>
          <div class="col">            
            <a href="#">
              <div class="brand-bg-color p-4 fs-3 rounded">
                <span>Major Exam</span>
              </div>
            </a>
          </div>
        </div>
      </div>

    </main>
  </div>

  <script src="./js/main.js"></script>
  
</body>
</html>