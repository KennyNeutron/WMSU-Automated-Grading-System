<?php 

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login.php');
}

require_once './classes/signin.class.php';
require_once './tools/functions.php';

?>

<!DOCTYPE html>
<html lang="en">
<?php 
	$title = 'Account Settings';
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
            <span class="fs-2 h1 m-0">Account Settings</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <form>
          <div class="row row-cols-1 row-cols-md-2">
            <div class="col">
              <div class="mb-3">
                <label for="emp_id" class="form-label">Employee ID</label>
                <input type="text" class="form-control" id="emp_id" aria-describedby="emp_id" value="<?= ucwords($_SESSION['emp_id']) ?>">
              </div>
              <div class="mb-3">
                <label for="lname" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lname" aria-describedby="lname" value="<?= ucwords($_SESSION['l_name']) ?>">
              </div>
              <div class="mb-3">
                <label for="fname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="fname" aria-describedby="fname" value="<?= ucwords($_SESSION['f_name']) ?>">
              </div>
              <div class="mb-3">
                <label for="mname" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="mname" aria-describedby="mname" value="<?= ucwords($_SESSION['m_name']) ?>">
              </div>
            </div>

            <div class="col">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" aria-describedby="email" value="<?= ucwords($_SESSION['email']) ?>">
              </div>
              <div class="mb-3">
                <label for="acad_rank" class="form-label">Academic Rank</label>
                <input type="text" class="form-control" id="acad_rank" aria-describedby="acad_rank" value="<?= ucwords($_SESSION['acad_rank']) ?>" >
              </div>
              <div class="mb-3">
                <label for="designation" class="form-label">Designation</label>
                <input type="text" class="form-control" id="designation" aria-describedby="designation" value="designation">
              </div>
              <div class="mb-3">
                <label for="extension" class="form-label">Extension</label>
                <select type="button" class="btn border dropdown-toggle form-select" data-bs-toggle="dropdown">
                  <option>Regular Lecturer</option>
                  <option>Visiting Lecturer</option>
                </select>
              </div>
            </div>
          </div>
          <div class="mt-2 text-end">
            <button type="button" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn brand-bg-color">Save</button>
          </div>
        </form>
      </div>

    </main>
  </div>

  <script src="./js/main.js"></script>
  <script src="./js/student_table.js"></script>
  
</body>
</html>