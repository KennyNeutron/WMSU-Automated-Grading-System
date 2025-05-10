<?php 

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
  header('location: ./login.php');
}

?>
<?php 
    // Including the database connection file
    include_once("./classes/database.php");

    // Check if studentid parameter exists in the URL
    if(isset($_GET['studentid'])) {
        // Getting id of the data from the URL
        $studentid = $_GET['studentid'];

        // Create an instance of the Database class
        $database = new Database();

        // Establish the database connection
        $connection = $database->connect();

        // Check if the connection is successful
        if ($connection) {
            // Prepare the SQL statement to select data associated with the particular id
            $stmt = $connection->prepare("SELECT * FROM student WHERE studentid = ?");
            // Bind parameters
            $stmt->bindParam(1, $studentid);
            // Execute the query
            $stmt->execute();

            // Fetch the data
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if data is fetched successfully
            if($res) {
                $lname = $res['lname'];
                $fname = $res['fname'];
                $mname = $res['mname'];
                $studentid = $res['studentid'];
                $studentemail = $res['studentemail'];
                $extension = $res['extension'];
            } else {
                echo "No student found with the provided ID.";
            }
        } else {
            echo "Failed to connect to the database.";
        }
    } else {
        echo "Student ID is missing in the URL.";
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
            <span class="fs-2 h1 m-0">Edit Student</span>
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

               <form action="../tools/edit_student_form.php" method="post" name="studenteditform">
                <div class="row row-cols-1 row-cols-md-2">
                    <div class="col">
                    <div class="mb-3">
                        <label for="lname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="lname" id="lname" aria-describedby="lname" value="<?php echo isset($lname) ? $lname : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="fname" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="fname" id="fname" aria-describedby="fname" value="<?php echo isset($fname) ? $fname : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="mname" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="mname" name="mname" aria-describedby="mname" value="<?php echo isset($mname) ? $mname : ''; ?>">
                    </div>
                    </div>

                    <div class="col">
                    <div class="mb-3">
                        <label for="extension" class="form-label">Extension</label>
                        <select class="btn border dropdown-toggle form-select" data-bs-toggle="dropdown" id="extension" name="extension">
                        <option <?php echo (isset($extension) && $extension == 'N/A') ? 'selected' : ''; ?>>N/A</option>
                        <option <?php echo (isset($extension) && $extension == 'Jr.') ? 'selected' : ''; ?>>Jr.</option>
                        <option <?php echo (isset($extension) && $extension == 'Sr.') ? 'selected' : ''; ?>>Sr.</option>
                        <option <?php echo (isset($extension) && $extension == 'II') ? 'selected' : ''; ?>>II</option>
                        <option <?php echo (isset($extension) && $extension == 'III') ? 'selected' : ''; ?>>III</option>
                        <option <?php echo (isset($extension) && $extension == 'IV') ? 'selected' : ''; ?>>IV</option>
                        <option <?php echo (isset($extension) && $extension == 'V') ? 'selected' : ''; ?>>V</option>
                        <option <?php echo (isset($extension) && $extension == 'VI') ? 'selected' : ''; ?>>VI</option>
                        <option <?php echo (isset($extension) && $extension == 'VII') ? 'selected' : ''; ?>>VII</option>
                        <option <?php echo (isset($extension) && $extension == 'IX') ? 'selected' : ''; ?>>IX</option>
                        <option <?php echo (isset($extension) && $extension == 'X') ? 'selected' : ''; ?>>X</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="studentid" class="form-label">Student ID</label>
                        <input type="text" class="form-control" id="studentid" name="studentid" aria-describedby="studentid" value="<?php echo isset($studentid) ? $studentid : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="studentemail" class="form-label">Student Email</label>
                        <input type="text" class="form-control" id="studentemail" name="studentemail" aria-describedby="studentemail" value="<?php echo isset($studentemail) ? $studentemail : ''; ?>">
                    </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" name="Cancel" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="Update" value="Update" class="btn brand-bg-color">Update</button>
                </div>
            </form>

      </div>

    </main>
  </div>

  <script src="./js/main.js"></script>
  <script src="./js/student_table.js"></script>
  
</body>
</html>




 

