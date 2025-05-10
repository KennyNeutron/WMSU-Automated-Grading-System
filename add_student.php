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
  $student_page = 'active';
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
            <span class="fs-2 h1 m-0">Add Student</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-4">
          <div class="col">
            <select type="button" class="btn border dropdown-toggle form-select border-danger mb-4" data-bs-toggle="dropdown">
              <option>CS140</option>
              <option>CS137</option>
            </select>
          </div>
        </div>
        <form action="./students.php">
          <div class="row row-cols-1 row-cols-md-2">
            <div class="col">
              <div class="mb-3">
                <label for="lname" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="fname" aria-describedby="fname" >
              </div>
              <div class="mb-3">
                <label for="fname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="fname" aria-describedby="fname">
              </div>
              <div class="mb-3">
                <label for="mname" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="fname" aria-describedby="fname">
              </div>
            </div>

            <div class="col">
              <div class="mb-3">
                <label for="extension" class="form-label">Extension</label>
                <select type="button" class="btn border dropdown-toggle form-select" data-bs-toggle="dropdown">
                  <option>Jr</option>
                  <option>Junior</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="studentid" class="form-label">Student ID</label>
                <input type="text" class="form-control" id="fname" aria-describedby="fname">
              </div>
              <div class="mb-3">
                <label for="studentemail" class="form-label">Student Email</label>
                <input type="text" class="form-control" id="fname" aria-describedby="fname">
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn brand-bg-color">Submit</button>
          </div>
        </form>
      </div>

    </main>
  </div>

  <script src="./js/main.js"></script>
  <script src="./js/student_table.js"></script>
  
</body>
</html>