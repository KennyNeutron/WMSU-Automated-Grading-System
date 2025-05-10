<div class="d-flex flex-column flex-shrink-0 p-3 bg-light border-end border-dark position-fixed" style="max-width: 215px; height: 100%; background-color: #EDEDED;">
  <a href="./index.php" class="d-flex align-items-center justify-content-center mb-md-0 link-dark text-decoration-none">
    <img src="./img/wmsu_logo.png" class="me-2" alt="" width="50px" height="50px">
    <span class="fs-2 h1 m-0">WMSU</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item">
      <a href="./index.php" class="nav-link link-dark d-flex align-items-center mb-2  <?= $home_page ?>" aria-current="page">
        <i class='bx bx-home-alt-2 fs-3'></i>
        <span class="fs-6 ms-2">Home</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="./select_subject_students" class="nav-link link-dark d-flex align-items-center mb-2 <?= $student_page ?>">
        <i class='bx bx-user fs-3'></i>
        <span class="fs-6 ms-2">Students</span>
      </a>
    </li>
    <!-- <li class="nav-item">
      <a href="./grade_posted-list" class="nav-link link-dark d-flex align-items-center mb-2  <?= $grade_page ?>">
        <i class='bx bx-clipboard fs-3'></i>
        <span class="fs-6 ms-2">Grade Posted</span>          
      </a>
    </li> -->
    <li class="nav-item">
      <a href="./select_subject_setting" class="nav-link link-dark d-flex align-items-center mb-2  <?= $sub_setting_page ?>">
        <i class='bx bx-cog fs-3'></i>
        <span class="fs-6 ms-2">Subject Settings</span>          
      </a>
    </li>
  </ul>
</div>