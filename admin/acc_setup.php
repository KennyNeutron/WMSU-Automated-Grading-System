<?php 

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login');
}

?>

<!DOCTYPE html>
<html lang="en">
<?php 
	$title = 'Profiling';
  $acc_setup = 'active';
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
      <div class="header" >
      <?php
        require_once('../includes/admin_header.php')
      ?>
      </div>
      
      <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;" >
        <div class="d-flex align-items-center">
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 fw-bold h1 m-0 brand-color">Account Setup</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="content container-fluid mw-100 border rounded shadow p-3">

          <div class="search-keyword col-12 flex-lg-grow-0 d-flex mb-2">

            <div class="input-group">
              <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control">
              <button class="btn btn-outline-secondary brand-bg-color" type="button"><i class='bx bx-search' aria-hidden="true" ></i></button>
            </div>
          </div>
          
          <?php
            $student_array = array(
              array(
                'emp_id' => '2019-0001',
                'last_name' => 'Carlos',
                'frist_name' => 'Juan',
                'mid_i' => 'O.',
                'email' => 'juancarlos@gmail.com',
                'designation' => 'Professor',
                'acad_rank' => 'Professor II',
                'faculty_type' => 'Regular',
              ),
            );
          ?>
          <table id="acc_setup" class="table table-striped table-sm" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th>Employee ID</th> <!--Code & description-->
                <th>Last Name</th>
                <th>First Name Rank</th>
                <th>M.I</th>
                <th>Email</th>
                <th>Designation</th>
                <th>Academic Rank</th>
                <th>Faculty Type</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $counter = 1;
                foreach ($student_array as $item){
              ?>
                <tr>
                  <td><?= $counter ?></td>
                  <td><?= $item['emp_id'] ?></td>
                  <td><?= $item['last_name'] ?></td>
                  <td><?= $item['frist_name'] ?></td>
                  <td><?= $item['mid_i'] ?></td>
                  <td><?= $item['email'] ?></td>
                  <td><?= $item['designation'] ?></td>
                  <td><?= $item['acad_rank'] ?></td>
                  <td><?= $item['faculty_type'] ?></td>
                  <td class="text-center d-flex">
                    <a href="# "><span class="text-success">accept</span></a>
                    <span class="text-danger">decline</span>
                  </td>
                </tr>
              <?php
                $counter++;
                }
              ?>
            </tbody>
          </table>

        </div>
        
      </div>

    </main>
  </div>

  <script src="./js/main.js"></script>
  <script src="./js/acc_setup-table.js"></script>
</body>
</html>