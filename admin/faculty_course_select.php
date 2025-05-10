<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

require_once '../classes/college.class.php';

$college = new College();
$keyword = $_GET['keyword'] ?? '';
$is_search = !empty($keyword);

// Retrieve departments based on search or default view
$departments = $is_search ? $college->searchByDeptName($keyword) : $college->showWithCourse();
?>
<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Admin | Faculty';
$faculty_page = 'active';
include '../includes/admin_head.php';
?>

<body>
    <div class="home">
        <div class="side">
            <?php require_once('../includes/admin_sidepanel.php'); ?>
        </div>
        <main>
            <div class="header">
                <?php require_once('../includes/admin_header.php'); ?>
            </div>

            <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
                <div class="d-flex align-items-center">
                    <button onclick="history.back()" class="bg-none"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></button>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class='fs-2 fw-bold h1 m-0 brand-color'>
                            Select Department
                        </span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <div class="search-keyword col-12 flex-lg-grow-0 d-flex justify-content-end mb-4">
                    <div class="input_width d-flex" style="width: 40% !important;">
                        <form id="searchForm" method="GET" action="" style="width: 100% !important;">
                            <div class="input-group">
                                <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control"
                                    value="<?= htmlspecialchars($keyword) ?>">
                                <button class="btn btn-outline-secondary brand-bg-color" type="submit"><i
                                        class='bx bx-search' aria-hidden="true"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($is_search && empty($departments)): ?>
                    <p class="text-center">No departments found for the keyword "<?= htmlspecialchars($keyword) ?>".</p>
                <?php elseif (!empty($departments)): ?>
                    <?php foreach ($departments as $item):
                        // Split the department data into individual entries
                        $departmentDataArray = explode(', ', $item['department_data']);
                        ?>
                        <div class="col-12 mb-1 mt-4">
                            <h4>College of <?= htmlspecialchars($item['college_name']) ?></h4>
                            <hr>
                        </div>
                        <?php if (!empty($departmentDataArray)): ?>
                            <div class="col-12 mb-5">
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                    <?php
                                    $hasValidDepartment = false;
                                    foreach ($departmentDataArray as $department):
                                        // Ensure that the department string contains ":"
                                        $parts = explode(':', $department);

                                        if (count($parts) < 2) {
                                            continue; // Skip if the format is invalid
                                        }

                                        list($departmentId, $departmentName) = $parts;
                                        $hasValidDepartment = true;
                                        ?>
                                        <div class="col">
                                            <a href="./faculty?department_id=<?= htmlspecialchars($departmentId) ?>">
                                                <div
                                                    class="d-flex align-items-center justify-content-center brand-bg-color p-4 fs-4 h-100 rounded">
                                                    <span><?= htmlspecialchars($departmentName) ?></span>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php if (!$hasValidDepartment): ?>
                                <p>No departments available.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p>No departments available.</p>
                        <?php endif; ?>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No departments available.</p>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchForm = document.getElementById("searchForm");
            const keywordInput = document.getElementById("keyword");

            searchForm.addEventListener("submit", (event) => {
                const keyword = keywordInput.value.trim();
                const urlParams = new URLSearchParams(window.location.search);

                if (keyword) {
                    urlParams.set("keyword", keyword);
                } else {
                    urlParams.delete("keyword");
                }

                window.location.search = urlParams.toString();
            });
        });
    </script>
</body>

</html>