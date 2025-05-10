<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header('location: ./login.php');
    exit();
}

require_once './classes/faculty_subs.class.php';
require_once './classes/period.class.php';
require_once './classes/component.class.php';

$keyword = $_GET['keyword'] ?? '';

$fac_subs = new Faculty_Subjects();
$period = new Periods();
$components = new SubjectComponents();

$all_subs = empty($keyword) ? $fac_subs->getByUser($_SESSION['emp_id']) : $fac_subs->searchByDeptName($_SESSION['emp_id'], $keyword);

$is_search = !empty($keyword);

$schoolYears = [];
$semesters = [];

foreach ($all_subs as $sub) {
    if (!in_array($sub['school_yr'], $schoolYears)) {
        $schoolYears[] = $sub['school_yr'];
    }
    if (!in_array($sub['semester'], $semesters)) {
        $semesters[] = $sub['semester'];
    }
}

rsort($schoolYears);
sort($semesters);
$selectedYear = $_GET['school_yr'] ?? null;
$selectedSemester = $_GET['semester'] ?? null;

$filteredSubs = array_filter($all_subs, function ($sub) use ($selectedYear, $selectedSemester) {
    return (!$selectedYear || $sub['school_yr'] == $selectedYear) &&
        (!$selectedSemester || $sub['semester'] == $selectedSemester);
});

usort($filteredSubs, function ($a, $b) {
    return ($b['school_yr'] <=> $a['school_yr']) ?: ($a['semester'] <=> $b['semester']);
});
?>
<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Subject Settings';
$sub_setting_page = 'active';
include './includes/head.php';
?>

<body>
    <div class="home">
        <div class="side">
            <?php require_once('./includes/sidepanel.php'); ?>
        </div>
        <main>
            <div class="header">
                <?php require_once('./includes/header.php'); ?>
            </div>

            <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
                <div class="d-flex align-items-center">
                    <div class="container-fluid d-flex justify-content-center">
                        <span class='fs-2 fw-bold h1 m-0 brand-color'>Select Subject (Settings)</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <div class="search-keyword col-12 flex-lg-grow-0 d-flex justify-content-between mb-4">
                    <div class="btn-group gap-3">
                        <div class="dropdown">
                            <button type="button" class="btn border border-danger dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <?= $selectedYear ? "S.Y. " . $selectedYear : "Select School Year" ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?">Default</a></li>
                                <?php foreach ($schoolYears as $year): ?>
                                    <li>
                                        <a class="dropdown-item school-year-option" href="?school_yr=<?= $year ?>" data-year="<?= $year ?>"
                                            data-first-semester="<?= $semesters[0] ?>">
                                            <?= "S.Y. " . $year ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="dropdown">
                            <button type="button" class="btn border border-danger dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <?= $selectedSemester ? $selectedSemester : ($selectedYear ? $semesters[0] : "Select Semester") ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="?<?= $selectedYear ? "school_yr=$selectedYear" : "" ?>">Default</a>
                                </li>
                                <?php foreach ($semesters as $sem): ?>
                                    <li><a class="dropdown-item"
                                            href="?<?= $selectedYear ? "school_yr=$selectedYear&" : "" ?>semester=<?= $sem ?>"><?= $sem ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="input_width d-flex" style="width: 40% !important;">
                        <form id="searchForm" method="GET" action="" style="width: 100% !important;">
                            <div class="input-group">
                                <input type="text" name="keyword" id="keyword" placeholder="Search" class="form-control"
                                    value="<?= htmlspecialchars($keyword) ?>">
                                <button class="btn btn-outline-secondary brand-bg-color" type="submit">
                                    <i class='bx bx-search' aria-hidden="true"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (empty($keyword)): ?>
                    <?php
                    // Group subjects by school year and semester.
                    $groupedSubs = [];
                    foreach ($filteredSubs as $sub) {
                        $schoolYr = $sub['school_yr'];
                        $sem = $sub['semester'];
                        $groupedSubs[$schoolYr][$sem][] = $sub;
                    }
                    ?>
                    <?php if (!empty($groupedSubs)): ?>
                        <?php foreach ($groupedSubs as $schoolYr => $semesters): ?>
                            <hr>
                            <h3 class="mt-4">S.Y. <?= htmlspecialchars($schoolYr) ?></h3>
                            <?php foreach ($semesters as $semester => $subs): ?>
                                <h4 class="mt-3"><?= htmlspecialchars($semester) ?></h4>
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 mb-4">
                                    <?php foreach ($subs as $item): ?>
                                        <div class="col">
                                            <form method="POST" action="./subject_setting">
                                                <input type="hidden" name="faculty_sub_id" value="<?= htmlspecialchars($item['faculty_sub_id']) ?>">
                                                <button type="submit" class="border-0 w-100 bg-transparent">
                                                    <div class="d-flex align-items-center justify-content-center brand-bg-color p-4 fs-4 h-100 rounded">
                                                        <span><?= htmlspecialchars($item['sub_code'] . " (" . $item['yr_sec'] . ")") ?></span>
                                                    </div>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">No subjects available.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (empty($filteredSubs)): ?>
                        <p class="text-center">No subjects found for "<?= htmlspecialchars($keyword) ?>".</p>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                            <?php foreach ($filteredSubs as $item): ?>
                                <div class="col">
                                    <form method="POST" action="./subject_setting">
                                        <input type="hidden" name="faculty_sub_id" value="<?= htmlspecialchars($item['faculty_sub_id']) ?>">
                                        <button type="submit" class="border-0 w-100 bg-transparent">
                                            <div class="d-flex align-items-center justify-content-center brand-bg-color p-4 fs-4 h-100 rounded">
                                                <span><?= htmlspecialchars($item['sub_code'] . " (" . $item['yr_sec'] . ")") ?></span>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <?php require_once('./includes/js.php'); ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchForm = document.getElementById("searchForm");
            const keywordInput = document.getElementById("keyword");

            searchForm.addEventListener("submit", (event) => {
                event.preventDefault();
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