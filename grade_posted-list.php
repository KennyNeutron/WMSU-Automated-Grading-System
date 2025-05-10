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
          <button onclick="history.back()" class="bg-none" ><i class='bx bx-chevron-left fs-2 brand-color'></i></button>
          <div class="container-fluid d-flex justify-content-center">
            <span class="fs-2 h1 m-0">CS 137</span>
          </div>
        </div>
      </div>

      <div class="m-4">
        <div class="table-responsive overflow-hidden">
          <div id="MyButtons" class="d-flex mb-2"></div>
          <div class="search-keyword col-12 flex-lg-grow-0 d-flex">
            
            <div class="form-group col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0 ms-lg-auto">
              <select name="student-point_eqv" id="student-point_eqv" class="form-select me-md-2">
                <option value="">Point Eqv.</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="5">5</option>
                
              </select>
            </div>

            <div class="form-group mx-4 col-12 col-sm-auto flex-sm-grow-1 flex-lg-grow-0">
              <select name="student-remark" id="student-remark" class="form-select me-md-2">
                <option value="">Remarks</option>
                <option value="Passed">Passed</option>
                <option value="Failed">Failed</option>
              </select>
            </div>
            
            <div class="input-group">
              <input type="text" name="keyword" id="keyword" placeholder="Search Product" class="form-control">
              <button class="btn btn-outline-secondary brand-bg-color" type="button"><i class='bx bx-search' aria-hidden="true" ></i></button>
            </div>
          </div>
        </div>
        <?php
          $student_array = array(
            array(
              'Student ID' => '2021-00123',
              'Student Name' => 'Burnt Pisa',
              'Email' => 'example@email.com',
              'Grade' => '90',
              'Point Eqv' => '1.75',
              'Remark' => 'Passed',
            ),
            array(
              'Student ID' => '2021-04132',
              'Student Name' => 'Juan Nuaj',
              'Email' => 'juan@email.com',
              'Grade' => '82',
              'Point Eqv' => '2.50',
              'Remark' => 'Passed',
            ),
            array(
              'Student ID' => '2021-00003',
              'Student Name' => 'joe Gardo',
              'Email' => 'joe@email.com',
              'Grade' => '100',
              'Point Eqv' => '1',
              'Remark' => 'Passed',
            ),
          );
        ?>
        <table id="Student_grade" class="table table-striped table-sm" style="width:100%">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Student ID</th>
              <th scope="col">Student Name</th>
              <th scope="col">Email</th>
              <th scope="col">Grade</th>
              <th scope="col">Point Eqv.</th>
              <th scope="col">Reamarks</th>
              <th scope="col" width="5%">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $counter = 1;
              foreach ($student_array as $item){
            ?>
              <tr>
                <td><?= $counter ?></td>
                <td><?= $item['Student ID'] ?></td>
                <td><?= $item['Student Name'] ?></td>
                <td><?= $item['Email'] ?></td>
                <td><?= $item['Grade'] ?></td>
                <td><?= $item['Point Eqv'] ?></td>
                <td><?= $item['Remark'] ?></td>
                <td class="text-center">
                  <i class='bx bx-trash-alt btn' ></i>
                </td>
              </tr>
            <?php
              $counter++;
              }
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <?php
    require_once('./includes/js.php');
  ?>
  <script src="./js/student_grade-table.js"></script>
  
</body>
</html>