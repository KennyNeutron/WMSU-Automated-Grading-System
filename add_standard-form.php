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
            <span class="fs-2 h1 m-0">Add Quiz</span>
          </div>
        </div>
      </div>

      <div class="m-5 py-3">
        <form action="./subject-info.php">
          <div class="row row-cols-1 row-cols-md-2">
            <div class="col">
              <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" aria-describedby="fname" >
              </div>
              <div class="mb-3">
                <label for="quiz_no" class="form-label">Quiz no.</label>
                <input type="number" class="form-control" id="quiz_no" aria-describedby="fname">
              </div>
              <div class="mb-3">
                <label for="quiz_quant" class="form-label">Quiz Items quantity</label>
                <input type="number" class="form-control" id="quiz_quant" aria-describedby="fname">
              </div>
            </div>
          </div>
          <button onclick="history.back()" type="button" class="btn btn-secondary">Cancel</button>
          <button type="submit" class="btn brand-bg-color">Add</button>
        </form>
      </div>

    </main>
  </div>

  <script src="./js/main.js"></script>
  
</body>
</html>