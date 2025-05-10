<div id="sidebarMenu" class="sidepanel d-flex flex-column flex-shrink-0 p-3 border-end border-dark position-fixed"
  style="max-width: 215px; height: 100%; background-color: white;">
  <a href="./index" class="d-flex align-items-center justify-content-center mb-md-0 link-dark text-decoration-none">
    <img src="../img/wmsu_logo.png" class="me-2" alt="" width="50px" height="50px">
    <span class="fs-2 h1 m-0 brand-color ">Admin</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item">
      <a href="./index" class="nav-link link-dark d-flex align-items-center mb-2 <?= $curriculum_page ?>"
        aria-current="page">
        <i class='bx bxs-book-alt fs-4'></i>
        <span class="fs-6 ms-2">Curriculum</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="./faculty_course_select.php"
        class="nav-link link-dark d-flex align-items-center mb-2 <?= $faculty_page ?>">
        <i class='bx bx-calendar fs-4'></i>
        <span class="fs-6 ms-2 text-start">Faculty & Scheduling</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="./grade" class="nav-link link-dark d-flex align-items-center mb-2  <?= $grades_page ?>">
        <i class='bx bx-bar-chart-alt-2 fs-4'></i>
        <span class="fs-6 ms-2">Grades</span>
      </a>
    </li>

    <li class="nav-item">
      <button class="btn btn-toggle link-dark d-flex align-items-center mb-2 nav-link <?= $userfaculty_page ?>"
        data-bs-toggle="collapse" data-bs-target="#userfaculty_toggle"
        aria-expanded="<?= (strpos($_SERVER['REQUEST_URI'], '/manage_acc.php') !== false || strpos($_SERVER['REQUEST_URI'], '/profiling.php') !== false) ? 'true' : 'false' ?>">
        <i class='bx bxs-group fs-4'></i>
        <span class="fs-6 ms-2 text-start">Users & Profiling</span>
        <i class='bx bx-chevron-down'></i>
      </button>

      <?php
      // Determine if the dropdown should be open
      $show_collapse = strpos($_SERVER['REQUEST_URI'], '/manage_acc.php') !== false || strpos($_SERVER['REQUEST_URI'], '/profiling.php') !== false;

      // Determine active states
      $active_acc = strpos($_SERVER['REQUEST_URI'], '/manage_acc.php') !== false;
      $active_profiling = strpos($_SERVER['REQUEST_URI'], '/profiling.php') !== false;
      ?>

      <div class="collapse <?= $show_collapse ? 'show' : '' ?>" id="userfaculty_toggle">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
          <li class="mb-1">
            <a href="./manage_acc.php"
              class="link-dark nav-link d-flex align-items-center <?= $active_acc ? 'active' : '' ?>">
              <i class='bx bx-chevron-right'></i>
              <span>Manage User Accounts</span>
            </a>
          </li>
          <li class="mb-1">
            <a href="./profiling.php"
              class="link-dark nav-link d-flex align-items-center <?= $active_profiling ? 'active' : '' ?>">
              <i class='bx bx-chevron-right'></i>
              Profiling
            </a>
          </li>
        </ul>
        <hr>
      </div>
    </li>

    <!-- <li class="nav-item">
      <div class="btn-group d-flex flex-column">
        <div class="link-grp d-flex justify-content-between gap-1">
          <a href="./profiling.php"
            class="nav-link link-dark d-flex align-items-center mb-2 w-100 <?= $profiling_page ?>" type="button">
            <i class='bx bxs-user-detail fs-4'></i>
            <span class="fs-6 ms-2">Profiling</span>
          </a>
          <button class="btn btn-toggle link-dark d-flex align-items-center mb-2 nav-link <?= $profiling_page ?>"
            data-bs-toggle="collapse" data-bs-target="#profiling_toggle" aria-expanded="false">
            <i class='bx bx-chevron-down'></i>
          </button>
        </div>

        <?php
        require_once '../classes/department.class.php';
        require_once '../tools/functions.php';

        $department = new Department();
        $department_array = $department->show();

        $show_collapse = false;
        if (isset($_GET['department_id'])) {
          $show_collapse = true;
        }
        ?>

        <div
          class="collapse<?= ($show_collapse && strpos($_SERVER['REQUEST_URI'], 'profiling') !== false ? ' show' : '') ?>"
          id="profiling_toggle" style="">
          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
            <?php
            if ($department_array) {
              foreach ($department_array as $item) {
                $active = false;

                if (strpos($_SERVER['REQUEST_URI'], 'profiling') !== false && isset($_GET['department_id']) && $_GET['department_id'] == $item['department_id']) {
                  $active = true;
                }
                ?>
                <li>
                  <a href="./profiling?department_id=<?= $item['department_id'] ?>"
                    class="link-dark nav-link d-flex align-items-center <?= ($active ? ' active' : '') ?>">
                    <i class='bx bxs-right-arrow'></i>
                    <span class="fs-6 ms-2"><?= $item['department_name'] ?></span>
                  </a>
                </li>
                <?php
              }
            }
            ?>
          </ul>
          <hr>
        </div>
      </div>
    </li>

    <li class="nav-item">
      <div class="btn-group d-flex flex-column">
        <div class="link-grp d-flex justify-content-between gap-1">
          <a href="./manage_acc.php" class="nav-link link-dark d-flex align-items-center mb-2 w-100 <?= $manage_acc ?>"
            type="button">
            <i class='bx bxs-cog fs-4'></i>
            <span class="fs-6 ms-2">Manage Accounts</span>
          </a>
          <button class="btn btn-toggle link-dark d-flex align-items-center mb-2 nav-link <?= $manage_acc ?> "
            data-bs-toggle="collapse" data-bs-target="#mnge_acc_toggle" aria-expanded="false">
            <i class='bx bx-chevron-down'></i>
          </button>
        </div>

        <div class="collapse <?= ($acc_setup || $user_acc) ? 'show' : '' ?>" id="mnge_acc_toggle" style="">
          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
            <li>
              <a href="./acc_setup.php" class="link-dark nav-link <?= $acc_setup ?>">
                <i class='bx bx-git-commit'></i>
                <span class="fs-6 ms-2">Account Setup</span>
              </a>
            </li>
            <li>
              <a href="./user_acc.php" class="link-dark nav-link <?= $user_acc ?>">
                <i class='bx bxs-user-circle fs-4'></i>
                <span class="fs-6 ms-2">User Account</span>
              </a>
            </li>
          </ul>
          <hr>
        </div>
      </div>
    </li>

    <li class="nav-item">
      <div class="btn-group d-flex flex-column">
        <div class="link-grp d-flex justify-content-between gap-1">
          <a href="./faculty" class="nav-link link-dark d-flex align-items-center mb-2 w-100 <?= $faculty_page ?>">
            <i class='bx bxs-group fs-4'></i>
            <span class="fs-6 ms-2">Faculty</span>
          </a>
          <button class="btn btn-toggle link-dark d-flex align-items-center mb-2 nav-link <?= $faculty_page ?>"
            data-bs-toggle="collapse" data-bs-target="#faculty_toggle" aria-expanded="false">
            <i class='bx bx-chevron-down'></i>
          </button>
        </div>

        <?php
        require_once '../classes/department.class.php';
        require_once '../tools/functions.php';

        $department = new Department();
        $department_array = $department->show();

        $show_collapse = false;
        if (strpos($_SERVER['REQUEST_URI'], '/faculty?department_id=' . $item['department_id']) !== false) {
          $show_collapse = true;
        }
        ?>

        <div
          class="collapse<?= ($show_collapse && strpos($_SERVER['REQUEST_URI'], 'faculty') !== false ? ' show' : '') ?>"
          id="faculty_toggle" style="">
          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
            <?php
            if ($department_array) {
              foreach ($department_array as $item) {
                $active = false;

                if (strpos($_SERVER['REQUEST_URI'], '/faculty?department_id=' . $item['department_id']) !== false) {
                  $active = true;
                }
                ?>
                <li>
                  <a href="./faculty?department_id=<?= $item['department_id'] ?>"
                    class="link-dark nav-link d-flex align-items-center <?= ($active ? ' active' : '') ?>">
                    <i class='bx bxs-right-arrow'></i>
                    <span class="fs-6 ms-2"><?= $item['department_name'] ?></span>
                  </a>
                </li>
                <?php
              }
            }
            ?>
          </ul>
          <hr>
        </div>
      </div>
    </li> -->

    <li class="nav-item">
      <button class="btn btn-toggle link-dark d-flex align-items-center mb-2 nav-link <?= $department_page ?>"
        data-bs-toggle="collapse" data-bs-target="#department_toggle"
        aria-expanded="<?= (strpos($_SERVER['REQUEST_URI'], '/manage_college') !== false || strpos($_SERVER['REQUEST_URI'], '/manage_department') !== false) ? 'true' : 'false' ?>">
        <i class='bx bxs-building-house fs-4'></i>
        <span class="fs-6 ms-2 text-start">Colleges & Departments</span>
        <i class='bx bx-chevron-down'></i>
      </button>

      <?php
      // Determine if the dropdown should be open
      $show_collapse = strpos($_SERVER['REQUEST_URI'], '/manage_college') !== false || strpos($_SERVER['REQUEST_URI'], '/manage_department') !== false;

      // Determine active states
      $active_college = strpos($_SERVER['REQUEST_URI'], '/manage_college') !== false;
      $active_department = strpos($_SERVER['REQUEST_URI'], '/manage_department') !== false;
      ?>

      <div class="collapse <?= $show_collapse ? 'show' : '' ?>" id="department_toggle">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
          <li class="mb-1">
            <a href="./manage_college"
              class="link-dark nav-link d-flex align-items-center <?= $active_college ? 'active' : '' ?>">
              <i class='bx bx-chevron-right'></i>
              <span>Manage Colleges</span>
            </a>
          </li>
          <li>
            <a href="./manage_department"
              class="link-dark nav-link d-flex align-items-center <?= $active_department ? 'active' : '' ?>">
              <i class='bx bx-chevron-right'></i>
              Manage Departments
            </a>
          </li>
        </ul>
        <hr>
      </div>
    </li>

    <li class="nav-item">
      <a href="./admin_settings" class="nav-link link-dark d-flex align-items-center mb-2  <?= $setting_page ?>">
        <i class='bx bx-cog fs-4'></i>
        <span class="fs-6 ms-2">Settings</span>
      </a>
    </li>
  </ul>
  <hr>
  <div class="account dropdown">
    <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2"
      data-bs-toggle="dropdown" aria-expanded="false">
      <img src="../img/profile-img/<?= $_SESSION['profile_image'] ?>" alt=""
        style="width: 32px; height: 32px; object-fit: cover; border-radius:50%;" class="me-2">
      <strong class="text-truncate"
        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; max-width: calc(100% - 40px);"><?= $_SESSION['l_name'] ?>
        - <?= $_SESSION['f_name'] ?></strong>
    </a>
    <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
      <li><a class="dropdown-item" href="../logout.php">Sign out</a></li>
    </ul>
  </div>
</div>

<script>
  $(document).ready(function () {
    // Toggle collapse on button click
    $('.btn-toggle').click(function () {
      var collapseId = $(this).attr('data-bs-target');

      // Collapse all other dropdowns
      $('.collapse').not(collapseId).collapse('hide');
    });

    // Listen to collapse events to reset chevrons when a dropdown is closed
    $('.collapse').on('hidden.bs.collapse', function () {
      var btn = $('button[data-bs-target="#' + this.id + '"]');
      btn.find('.bx-chevron-down').removeClass('bx-rotate-180');
    });
  });

</script>