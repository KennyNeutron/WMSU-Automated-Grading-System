<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
    header('location: ./login.php');
    exit(); // Make sure to exit after redirection
}

require_once './tools/functions.php';
require_once './classes/profiling.class.php';
require_once './classes/faculty_sched.class.php';
require_once './classes/faculty_subs.class.php';
require_once './classes/students.class.php';
require_once './classes/grades.class.php';
require_once './classes/period.class.php';
require_once './classes/component.class.php';
require_once './classes/component_items.class.php';
require_once './classes/component_scores.class.php';

$profiling = new Profiling();
$sched = new Faculty_Sched();
$fac_subs = new Faculty_Subjects();
$students = new Students();
$studentsBySub = new Grades();
$period = new Periods();
$components = new SubjectComponents();
$comp_item = new ComponentItems();
$scores = new ComponentScores();

$selected_faculty_sub_id = isset($_GET['faculty_sub_id']) ? $_GET['faculty_sub_id'] : null;

$info = $profiling->fetchEMP($_SESSION['emp_id']);

$subject = $fac_subs->getProf($_GET['faculty_sub_id']);
$studentList = $studentsBySub->showBySubject($_GET['faculty_sub_id']);
$midtermComp = $period->showMidterm($selected_faculty_sub_id);
$finaltermComp = $period->showFinalterm($selected_faculty_sub_id);
$activePeriod = isset($_GET['active_period']) ? $_GET['active_period'] : 'midterm';
$selectedComponents = ($activePeriod === 'finalterm') ? $finaltermComp : $midtermComp;

$filter_component_type = isset($_GET['component_type']) ? $_GET['component_type'] : '';

// Filter components if a filter is set
if (!empty($filter_component_type)) {
    $filteredComponents = array_filter($selectedComponents, function ($comp) use ($filter_component_type) {
        return $comp['component_id'] == $filter_component_type;
    });
} else {
    $filteredComponents = $selectedComponents;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Edit Columns';
$home_page = 'active';
include './includes/head.php';
?>

<body>
    <div class="home">
        <div class="side">
            <?php include './includes/sidepanel.php'; ?>
        </div>
        <main>
            <div class="header">
                <?php include './includes/header.php'; ?>
            </div>

            <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
                <div class="d-flex align-items-center">
                    <a href="./subject_students.php?faculty_sub_id=<?= $selected_faculty_sub_id ?>" class="bg-none"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></a>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 h1 m-0">Edit Columns</span>
                    </div>
                </div>
            </div>

            <div class="container mt-4">
                <div class="search-keyword col-12 flex-lg-grow-0 d-flex justify-content-end mb-4">
                    <form id="filterForm" method="GET">
                        <!-- Retain necessary parameters -->
                        <input type="hidden" name="faculty_sub_id"
                            value="<?= htmlspecialchars($selected_faculty_sub_id) ?>">
                        <input type="hidden" name="active_period" value="<?= htmlspecialchars($activePeriod) ?>">

                        <label for="component_type" class="form-label">Filter by Column</label>
                        <select name="component_type" id="component_type" class="form-select" onchange="autoFilter()">
                            <option value="">All Columns</option>
                            <?php foreach ($selectedComponents as $comp): ?>
                                <option value="<?= $comp['component_id'] ?>"
                                    <?= ($filter_component_type == $comp['component_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($comp['component_type']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <?php if (!empty($filteredComponents)): ?>
                    <div class="row">
                        <?php foreach ($filteredComponents as $index => $component): ?>
                            <div class="col-12 mt-4">
                                <h4><?= htmlspecialchars($component['component_type']) ?></h4>
                                <hr>
                            </div>
                            <div class="col-12 mb-4">
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                    <?php
                                    $component_items = $comp_item->getItemById($component['component_id']);
                                    if (!empty($component_items)):
                                        foreach ($component_items as $item):
                                            ?>
                                            <div class="col">
                                                <div
                                                    class="column p-4 text-center brand-bg-color fs-4 h-100 rounded shadow-sm position-relative">
                                                    <div class="d-flex justify-content-between">
                                                        <span
                                                            class="fs-6 mb-2"><?= date('M d, Y', strtotime($item['component_date'])) ?></span>
                                                        <div class="dropdown-container position-absolute">
                                                            <i class='bx bx-dots-vertical-rounded fs-5 text-white position-absolute top-0'
                                                                id="dropdownMenuButton" style="cursor: pointer;"
                                                                data-bs-toggle="dropdown"></i>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="./edit_component_items?faculty_sub_id=<?= $selected_faculty_sub_id ?>&period=<?= $activePeriod ?>&items_id=<?= $item['items_id'] ?>&component_id=<?= $component['component_id'] ?>"
                                                                        class="dropdown-item">
                                                                        <i class='bx bx-edit text-success fs-6'></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <button class="delete-btn dropdown-item"
                                                                        data-subject-id="<?= $item['items_id'] ?>">
                                                                        <i class='bx bx-trash-alt text-danger fs-6'></i> Delete
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <p class="fs-4">
                                                        <?= htmlspecialchars($component['component_type']) ?> No.
                                                        <?= htmlspecialchars($item['component_no']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <?php
                                        endforeach;
                                    else:
                                        ?>
                                        <p class="text-center">No data available.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center">No column found.</p>
                <?php endif; ?>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteModalLabel"
                aria-hidden="true">
                <div id="alertContainer"></div>
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this column?</p>
                            <p class="text-danger"><strong>Warning:</strong> This will also delete all exisiting
                                data/grades associated
                                with this column.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dropdownContainers = document.querySelectorAll('.dropdown-container');

            dropdownContainers.forEach(container => {
                const menu = container.querySelector('.dropdown-menu');
                const trigger = container.querySelector('.bx-dots-vertical-rounded');

                trigger.addEventListener('click', event => {
                    event.stopPropagation();
                    const isVisible = menu.style.display === 'block';
                    document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                        otherMenu.style.display = 'none';
                    });
                    menu.style.display = isVisible ? 'none' : 'block';
                });
            });

            document.addEventListener('click', () => {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            });
        });

        function autoFilter() {
            const compType = document.getElementById('component_type').value;
            const urlParams = new URLSearchParams(window.location.search);
            if (compType) {
                urlParams.set('component_type', compType);
            } else {
                urlParams.delete('component_type');
            }
            window.location.search = urlParams.toString();
        }

        $(document).ready(function () {
            $('.delete-btn').on('click', function () {
                var items_id = $(this).data('subject-id');
                $('#confirmDeleteBtn').data('items-id', items_id);
                $('#deleteConfirmationModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function () {
                var items_id = $(this).data('items-id');
                
                $.ajax({
                    url: './delete_component_items.php',
                    method: 'POST',
                    data: {
                        items_id: items_id
                    },
                    success:
                        function (response) {
                            showAlert('Column deleted successfully!', 'success');
                            setTimeout(() => location.reload(), 1000);
                        },
                    error:
                        function (xhr, status, error) {
                            console.error(xhr.responseText);
                            alert('Error occurred: ' + error);
                        }
                });
            });

            function showAlert(message, type) {
                const alertContainer = document.getElementById('alertContainer');
                const alertHTML = `
            <div class="alert alert-${type} d-flex flex-row align-items-center gap-2 position-fixed top-0 start-50 translate-middle-x w-auto mt-4 z-index-1050" role="alert">
               <i class='bx bx-check-circle'></i> ${message}
            </div>
        `;
                alertContainer.innerHTML = alertHTML;

                // setTimeout(() => {
                //     alertContainer.innerHTML = '';
                // }, 1000);
            }
        });
    </script>
</body>

</html>