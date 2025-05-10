<header class="navbar navbar-dark sticky-top flex-md-nowrap p-0 w-100" style="background-color: #952323;">
  <button class="d-flex bg-transparent" type="button" id="collapse-side">
    <i class='bx bx-menu me-0 px-3 color-white fs-3' style="color: whitesmoke;"></i>
  </button>
  <button class="navbar-toggler d-md-none collapsed me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <nav class="navbar navbar-expand-md navbar-dark d-none d-md-block">
    <div class="container-fluid">
      <div class="navbar-collapse offcanvas-collapse">
        <ul class="navbar-nav me-auto d-flex align-items-center gap-2">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle px-0" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class='bx bx-bell fs-4' style="color:whitesmoke;"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton">
              <li class="border-bottom fs-6 fw-bold pb-2 text-center">Notification</li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle color-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="./img/profile-img/profile.png" class="rounded-circle me-1" alt="User Image" width="35px" height="35px">
            </a>
            <ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton">
              <li class="border-bottom fs-6 fw-bold pb-2 text-center"><?= ucwords($_SESSION['name']) ?></li>
              <li><a class="dropdown-item" href="./acc_setting.php">Account Settings</a></li>
              <li><a class="dropdown-item" href="./logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>
