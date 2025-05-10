<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
    header('location: ../login');
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

$faculty_sub_id = $_GET['faculty_sub_id'] ?? null;
$grades_id = $_GET['grades_id'] ?? null;

$selected_faculty_sub_id = isset($_GET['faculty_sub_id']) ? $_GET['faculty_sub_id'] : null;
$selected_tab = '';

$info = $profiling->fetchEMP($_SESSION['emp_id']);

$subject = $fac_subs->getProf($_GET['faculty_sub_id']);
$studentList = $studentsBySub->showBySubject($_GET['faculty_sub_id']);
$midtermComp = $period->showMidterm($selected_faculty_sub_id);
$finaltermComp = $period->showFinalterm($selected_faculty_sub_id);
///==========

$getAttendanceMidterm  = $period->getAttendanceMidtermm($selected_faculty_sub_id);
//==========
$sub_type = "";

if ($subject['subject_type'] == 'lecture') {
    $sub_type = ' - LEC';
} elseif ($subject['subject_type'] == 'laboratory') {
    $sub_type = ' - LAB';
} elseif ($subject['subject_type'] == 'combined') {
    $sub_type = '';
}
?>




<!DOCTYPE html>
<html lang="en">
<?php
$title = $subject['sub_code'];
$home_page = 'active';
include './includes/head.php';
?>

<body>
    <div class="home">
        <div class="side">
            <?php require_once('./includes/sidepanel.php') ?>
        </div>
        <main>
            <div class="header">
                <?php require_once('./includes/header.php') ?>
            </div>

            <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
                <div class="d-flex align-items-center">
                    <a href="./index" class="bg-none d-flex align-items-center">
                        <i class='bx bx-chevron-left fs-2 brand-color'></i>
                    </a>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Students List</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link text-dark active" id="nav-midterm-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-midterm" type="button" role="tab" aria-controls="nav-midterm"
                            aria-selected="true" onclick="updateURL('midterm')">Midterm</button>
                        <button class="nav-link text-dark" id="nav-finalterm-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-finalterm" type="button" role="tab" aria-controls="nav-finalterm"
                            aria-selected="false" onclick="updateURL('finalterm')">Final Term</button>
                    </div>
                </nav>

                <div class="tab-content py-4 px-3" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-midterm" role="tabpanel"
                        aria-labelledby="nav-midterm-tab">
                        <div class="content container-fluid mw-100 border rounded shadow p-3 position-relative">
                            <form id="settingsForm" method="POST" action="./subject_setting"
                                class="position-absolute top-0 end-0 m-2">
                                <input type="hidden" name="faculty_sub_id" value="<?= $selected_faculty_sub_id ?>">
                                <button type="submit" name="edit_criteria" class="btn btn-primary p-2 rounded">
                                    <i class='bx bxs-cog'></i> Settings
                                </button>
                            </form>


                            <div class="d-flex flex-column align-items-center">
                                <h3 class="brand-color"><?= ucwords($subject['sub_name']) ?></h3>
                                <h4 style="margin-bottom: 0;"><?= $subject['sub_code'] . $sub_type ?></h4>
                                <h4 style="margin: 0; padding: 0;">(<?= $subject['yr_sec'] ?>)</h4>
                            </div>

                            <div class="d-flex flex-row justify-content-end my-4">

                        

                                <a href="#" class="btn btn-outline-secondary btn-add ms-3 text-white btn btn-primary"
  
                                    data-bs-toggle="modal" 
                                    data-bs-target="#attendanceModal">
                                    Attendance
                                    </a>

                                <a class="btn btn-outline-secondary btn-add ms-3 brand-bg-color add-btn"
                                    href="./column_select?active_period=finalterm&faculty_sub_id=<?= $selected_faculty_sub_id ?>">
                                    Edit Columns
                                </a>
                                <button class="btn btn-outline-secondary btn-add ms-3 brand-bg-color add-btn"
                                    type="button" data-bs-toggle="modal" data-bs-target="#addComponentModal"
                                    data-period="finalterm">
                                    <i class='bx bx-plus-circle'></i>
                                </button>
                            </div>

                            <?php
                            // Pre-fetch component items for all components in $midtermComp
                            $componentItemsMap = [];
                            foreach ($midtermComp as $component) {
                                $componentId = $component['component_id'];
                                $componentItems = $comp_item->getItemById($componentId);
                                $componentItemsMap[$componentId] = $componentItems; // Store items for later use
                            }
                            ?>

                         

                        
                        <table id="student_table_midterm" class="table table-striped" style="width:125%">
                                <thead>
                                    <!-- Main Header Row -->
                                    <tr>
                                    <th >#</th>
                                    <th>Action</th> 
                                    <th >Student ID</th>
                                    <th >Student Name</th>
                                  
                                  
                                        <?php foreach ($midtermComp as $component): ?>
                                            <?php
                                            $componentId = $component['component_id'];
                                            $items = $componentItemsMap[$componentId]; // Use pre-fetched items
                                            $colspan = count($items);
                                            ?>
                                            <th colspan="<?= max(1, $colspan) ?>" class="text-center component-type-column">
                                                <?= ucwords($component['component_type']) ?>
                                            </th>
                                        <?php endforeach; ?>
                                   
                                    </tr>

                                    <!-- Subheader: Component Number & Date -->
                                    <tr>
                                    
                                        <th colspan="4"></th>
                                        <?php foreach ($midtermComp as $component): ?>
                                            <?php
                                            $componentId = $component['component_id'];
                                            $items = $componentItemsMap[$componentId];
                                            if (!empty($items)) {
                                                foreach ($items as $item): ?>
                                                    <th class="text-center component-type-column">
                                                        <div style="font-size: 12px;"><?= "No. " . $item['component_no'] ?></div>
                                                        <div style="font-size: 12px; color: gray;">
                                                            <?= date('M d, Y', strtotime($item['component_date'])) ?>
                                                        </div>
                                                    </th>
                                                <?php endforeach;
                                            } else { ?>
                                                <th class="text-center component-type-column">-</th>
                                            <?php } ?>
                                        <?php endforeach; ?>
                                        
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $counter = 1;
                                    foreach ($studentList as $student): ?>
                                        <tr>
                                        <td><?= $counter ?></td>
                                            <td>
                                            <button type="button" class="btn btn-sm btn-warning edit-btn" data-student-id="<?= $student['grades_id'] ?>">
                                                Edit
                                            </button>
                                            </td>
                                            <td>
                                                <a
                                                    href="./edit_grades?faculty_sub_id=<?= $selected_faculty_sub_id ?>&grades_id=<?= $student['grades_id'] ?>&active_period=<?= $_GET['active_period'] ?? 'midterm' ?>">
                                                    <?= $student['student_id'] ?>
                                                </a>
                                            </td>
                                            <td><?= ucwords($student['fullName']) ?></td>
                                           

                                           

                                            <?php foreach ($midtermComp as $component): ?>
                                                <?php
                                                $componentId = $component['component_id'];
                                                $items = $componentItemsMap[$componentId];
                                                if (!empty($items)) {
                                                    foreach ($items as $item):
                                                        $scoreData = $scores->getAllScoreByItemStud($student['grades_id'], $item['items_id']);
                                                        if (!empty($scoreData)) {
                                                            foreach ($scoreData as $score): ?>
                                                             <td class="text-center">
                                                                <span class="score-text" style="color:#952323;">
                                                                    <?= htmlspecialchars($score['score']) ?>
                                                                </span>
                                                                <input type="text"
                                                                    class="score-input"
                                                                    name="scores[<?= $student['grades_id'] ?>][<?= $item['items_id'] ?>]"
                                                                    value="<?= htmlspecialchars($score['score']) ?>"
                                                                    style="width: 40px; text-align: center; color:#952323; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; display: none;" />
                                                                <span class="text-secondary">/<?= $item['component_quantity'] ?></span>
                                                            </td>
                                                            <?php endforeach;
                                                        } else { ?>
                                                            <td class="text-center">
                                                                <span class="score-text" style="color:#952323;">0</span>
                                                                <input type="text"
                                                                    class="score-input"
                                                                    name="scores[<?= $student['grades_id'] ?>][<?= $item['items_id'] ?>]"
                                                                    value="0"
                                                                    style="width: 40px; text-align: center; color:#952323; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; display: none;" />
                                                                <span class="text-secondary">/<?= $item['component_quantity'] ?></span>
                                                            </td>
                                                        <?php }
                                                    endforeach;
                                                } else { ?>
                                                    <td class="text-center">-</td>
                                                <?php } ?>
                                            <?php endforeach; ?>
                                            
                                        </tr>
                                        <?php
                                        $counter++;
                                    endforeach;
                                    ?>
                               
                                </tbody>
                            </table>

                         

                        </div>
                    </div>

                    <div class="tab-pane fade" id="nav-finalterm" role="tabpanel" aria-labelledby="nav-finalterm-tab">
                        <div class="content container-fluid mw-100 border rounded shadow p-3 position-relative">
                            <form id="settingsForm" method="POST" action="./subject_setting"
                                class="position-absolute top-0 end-0 m-2">
                                <input type="hidden" name="faculty_sub_id" value="<?= $selected_faculty_sub_id ?>">
                                <button type="submit" name="edit_criteria" class="btn btn-primary p-2 rounded">
                                    <i class='bx bxs-cog'></i> Settings
                                </button>
                            </form>

                            <div class="d-flex flex-column align-items-center">
                                <h3 class="brand-color"><?= ucwords($subject['sub_name']) ?></h3>
                                <h4 style="margin-bottom: 0;"><?= $subject['sub_code'] . $sub_type ?></h4>
                                <h4 style="margin: 0; padding: 0;">(<?= $subject['yr_sec'] ?>)</h4>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button class="btn btn-outline-secondary btn-add ms-3 brand-bg-color add-btn"
                                        type="button" data-bs-toggle="modal" data-bs-target="#applyAllGradesModal"
                                        data-period="<?= $selected_tab ?>">
                                        <i class='bx bx-upload'></i> Apply All Grades
                                    </button>
                                </div>

                                <div class="d-flex flex-row justify-content-end my-4">
                                    <a class="btn btn-outline-secondary btn-add ms-3 brand-bg-color add-btn"
                                        href="./column_select?active_period=midterm&faculty_sub_id=<?= $selected_faculty_sub_id ?>">
                                        Edit Columns
                                    </a>
                                    <button class="btn btn-outline-secondary btn-add ms-3 brand-bg-color add-btn"
                                        type="button" data-bs-toggle="modal" data-bs-target="#addComponentModal"
                                        data-period="midterm">
                                        <i class='bx bx-plus-circle'></i>
                                    </button>
                                </div>
                            </div>

                            <?php
                            // Pre-fetch component items for all components in $midtermComp
                            $componentItemsMap = [];
                            foreach ($finaltermComp as $component) {
                                $componentId = $component['component_id'];
                                $componentItems = $comp_item->getItemById($componentId);
                                $componentItemsMap[$componentId] = $componentItems; // Store items for later use
                            }
                            ?>

                            <table id="student_table_finalterm" class="table table-striped" style="width:125%">
                                <thead>
                                    <!-- Main Header Row -->
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        
                                        <?php foreach ($finaltermComp as $component): ?>
                                            <?php
                                            $componentId = $component['component_id'];
                                            $items = $componentItemsMap[$componentId]; // Use pre-fetched items
                                            $colspan = count($items);
                                            ?>
                                            <th colspan="<?= max(1, $colspan) ?>" class="text-center component-type-column">
                                                <?= ucwords($component['component_type']) ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>

                                    <!-- Subheader: Component Number & Date -->
                                    <tr>
                                        <th colspan="3"></th>
                                        <?php foreach ($finaltermComp as $component): ?>
                                            <?php
                                            $componentId = $component['component_id'];
                                            $items = $componentItemsMap[$componentId]; // Use pre-fetched items
                                            if (!empty($items)) {
                                                foreach ($items as $item): ?>
                                                    <th class="text-center component-type-column">
                                                        <div style="font-size: 12px;"><?= "No. " . $item['component_no'] ?></div>
                                                        <div style="font-size: 12px; color: gray;">
                                                            <?= date('M d, Y', strtotime($item['component_date'])) ?>
                                                        </div>
                                                    </th>
                                                <?php endforeach;
                                            } else { ?>
                                                <th class="text-center component-type-column">-</th>
                                            <?php } ?>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $counter = 1;
                                    foreach ($studentList as $student): ?>
                                        <tr>
                                            <td><?= $counter ?></td>
                                            <td>
                                                <a
                                                    href="./edit_grades?faculty_sub_id=<?= $selected_faculty_sub_id ?>&grades_id=<?= $student['grades_id'] ?>&active_period=<?= $_GET['active_period'] ?? 'midterm' ?>">
                                                    <?= $student['student_id'] ?>
                                                </a>
                                            </td>
                                            <td><?= ucwords($student['fullName']) ?></td>
                                           

                                            <?php foreach ($finaltermComp as $component): ?>
                                                <?php
                                                $componentId = $component['component_id'];
                                                $items = $componentItemsMap[$componentId]; // Use pre-fetched items
                                                if (!empty($items)) {
                                                    foreach ($items as $item):
                                                        $scoreData = $scores->getAllScoreByItemStud($student['grades_id'], $item['items_id']);
                                                        if (!empty($scoreData)) {
                                                            foreach ($scoreData as $score): ?>
                                                                <td class="text-center">
                                                                    <span
                                                                        style="color:#952323;"><?= htmlspecialchars($score['score']) ?></span><span
                                                                        class="text-secondary"><?= '/' . $item['component_quantity'] ?></span>
                                                                </td>
                                                            <?php endforeach;
                                                        } else { ?>
                                                            <td class="text-center">0/<?= $item['component_quantity'] ?></td>
                                                        <?php }
                                                    endforeach;
                                                } else { ?>
                                                    <td class="text-center">-</td>
                                                <?php } ?>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php
                                        $counter++;
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <div class="modal fade" id="addComponentModal" tabindex="-1" aria-labelledby="addComponentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addComponentModalLabel">Add Column</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <?php
                                $facultySubId = $selected_faculty_sub_id;

                                $activePeriod = isset($_GET['active_period']) ? $_GET['active_period'] : 'midterm';
                                $selectedComponents = ($activePeriod === 'finalterm') ? $finaltermComp : $midtermComp;

                                if (!empty($selectedComponents)):
                                    foreach ($selectedComponents as $component):
                                        ?>
                                        <div class="col-6">
                                            <a href="./add_component_items?faculty_sub_id=<?= $facultySubId ?>&component_id=<?= $component['component_id'] ?>"
                                                class="d-flex align-items-center justify-content-center brand-bg-color w-100 p-3 mb-2 rounded">
                                                <?= ucwords($component['component_type']) ?>
                                            </a>
                                        </div>
                                        <?php
                                    endforeach;
                                else:
                                    ?>
                                    <div class="col-12 text-center">
                                        <p>No criteria available</p>
                                        <form method="POST" action="./subject_setting" style="display:inline;">
                                            <input type="hidden" name="faculty_sub_id"
                                                value="<?= htmlspecialchars($facultySubId) ?>">
                                            <button type="submit" name="edit_criteria" class="btn btn-primary p-2 rounded">
                                                Add Criteria
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="applyAllGradesModal" tabindex="-1" aria-labelledby="applyAllGradesModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="applyAllGradesModalLabel">Apply All Grades</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to apply grades for all students in this subject?
                        <span class="text-danger">This action cannot be undone, make sure all grades are graded correctly.</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" action="./apply_all_grades.php" style="display: inline;">
                            <input type="hidden" name="faculty_sub_id" value="<?= $selected_faculty_sub_id ?>">
                            <input type="hidden" name="active_period"
                                value="<?= $_GET['active_period'] ?? 'midterm' ?>">
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Modal -->
        <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="attendanceModalLabel">Attendance Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="attendanceContent">
     
       
      <?php
                            // Pre-fetch component items for all components in $midtermComp
                            $componentItemsMap = [];
                            foreach ($getAttendanceMidterm  as $component) {
                                $componentId = $component['component_id'];
                                $componentItems = $comp_item->getItemById($componentId);
                                $componentItemsMap[$componentId] = $componentItems; // Store items for later use
                            }
                            ?>

                         

                            <table id="student_table_midterm" class="table table-striped" style="width:100%">
                                <thead>
                                    <!-- Main Header Row -->
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                       
                                  
                                        <?php foreach ($getAttendanceMidterm  as $component): ?>
                                            <?php
                                            $componentId = $component['component_id'];
                                            $items = $componentItemsMap[$componentId]; // Use pre-fetched items
                                            $colspan = count($items);
                                            ?>
                                            <th colspan="<?= max(1, $colspan) ?>" class="text-center component-type-column">
                                                <?= ucwords($component['component_type']) ?>
                                            </th>
                                        <?php endforeach; ?>
                                   
                                    </tr>

                                    <!-- Subheader: Component Number & Date -->
                                    <tr>
                                    
                                        <th colspan="4"></th>
                                        <?php foreach ($getAttendanceMidterm  as $component): ?>
                                            <?php
                                            $componentId = $component['component_id'];
                                            $items = $componentItemsMap[$componentId];
                                            if (!empty($items)) {
                                                foreach ($items as $item): ?>
                                                    <th class="text-center component-type-column">
                                                        <div style="font-size: 12px;"><?= "No. " . $item['component_no'] ?></div>
                                                        <div style="font-size: 12px; color: gray;">
                                                            <?= date('M d, Y', strtotime($item['component_date'])) ?>
                                                        </div>
                                                    </th>
                                                <?php endforeach;
                                            } else { ?>
                                                <th class="text-center component-type-column">-</th>
                                            <?php } ?>
                                        <?php endforeach; ?>
                                        
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $counter = 1;
                                    foreach ($studentList as $student): ?>
                                        <tr>
                                            <td><?= $counter ?></td>
                                            <td>
                                                <a
                                                    href="./edit_grades?faculty_sub_id=<?= $selected_faculty_sub_id ?>&grades_id=<?= $student['grades_id'] ?>&active_period=<?= $_GET['active_period'] ?? 'midterm' ?>">
                                                    <?= $student['student_id'] ?>
                                                </a>
                                            </td>
                                            <td><?= ucwords($student['fullName']) ?></td>
                                          

                                           

                                            <?php foreach ($getAttendanceMidterm as $component): ?>
                                                <?php
                                                $componentId = $component['component_id'];
                                                $items = $componentItemsMap[$componentId];
                                                if (!empty($items)) {
                                                    foreach ($items as $item):
                                                        $scoreData = $scores->getAllScoreByItemStud($student['grades_id'], $item['items_id']);
                                                        if (!empty($scoreData)) {
                                                            foreach ($scoreData as $score): ?>
                                                                <td class="text-center">
                                                                    <span
                                                                        style="color:#952323;"><?= htmlspecialchars($score['score']) ?></span><span
                                                                        class="text-secondary"><?= '/' . $item['component_quantity'] ?></span>
                                                                </td>
                                                            <?php endforeach;
                                                        } else { ?>
                                                            <td class="text-center">0/<?= $item['component_quantity'] ?></td>
                                                        <?php }
                                                    endforeach;
                                                } else { ?>
                                                    <td class="text-center">-</td>
                                                <?php } ?>
                                            <?php endforeach; ?>
                                            
                                        </tr>
                                        <?php
                                        $counter++;
                                    endforeach;
                                    ?>
                               
                                </tbody>
                            </table>
 
      </div>
    </div>
  </div>
</div>


    </div>

    <script src="./js/main.js"></script>
    <script>
        $(document).ready(function () {
            $('#student_table_midterm').DataTable({
                pageLength: 10,
                scrollX: true,
                lengthChange: false,
                columnDefs: [{
                    targets: [1, 2, 3],
                    orderable: true,
                }]
            });

            $('#student_table_finalterm').DataTable({
                pageLength: 10,
                scrollX: true,
                lengthChange: false,
                columnDefs: [{
                    targets: [1, 2, 3],
                    orderable: true,
                }]
            });
        });


        function updateURL(period) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('active_period', period);
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        }

        document.addEventListener('DOMContentLoaded', function () {
            function saveActiveTab(tabId) {
                localStorage.setItem('activeTab', tabId);
            }

            function restoreActiveTab() {
                const activeTabId = localStorage.getItem('activeTab');
                if (activeTabId) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const currentPeriod = urlParams.get('active_period');

                    if (activeTabId === "nav-midterm-tab" && currentPeriod !== 'midterm') {
                        urlParams.set('active_period', 'midterm');
                        window.location.replace(window.location.pathname + '?' + urlParams.toString());
                        return; // End function to prevent further execution
                    } else if (activeTabId === "nav-finalterm-tab" && currentPeriod !== 'finalterm') {
                        urlParams.set('active_period', 'finalterm');
                        window.location.replace(window.location.pathname + '?' + urlParams.toString());
                        return; // End function to prevent further execution
                    }

                    // If URL is already correct, just activate the tab
                    const tabTrigger = new bootstrap.Tab(document.querySelector(`#${activeTabId}`));
                    tabTrigger.show();
                }
            }

            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const tabId = this.getAttribute('id');
                    saveActiveTab(tabId);
                });
            });

            // Restore the active tab on page load
            restoreActiveTab();
        });

    </script>

<!-- =============== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-btn');

    editButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const parentRow = this.closest('tr');
            const scoreTexts = parentRow.querySelectorAll('.score-text');
            const scoreInputs = parentRow.querySelectorAll('.score-input');

            scoreTexts.forEach(span => span.style.display = 'none');
            scoreInputs.forEach(input => input.style.display = 'inline-block');

            // Change button text to "Save"
            this.innerText = 'Save';
            this.classList.remove('btn-warning');
            this.classList.add('btn-success');

            // Optional: After Save click, you can submit the form or capture new data
        });
    });
});
</script>



<style>
.scrollable-table-container {
    overflow-x: auto;
    width: 100%;
}

/* Base styling */
#student_table_midterm th,
#student_table_midterm td {
    white-space: nowrap;
    vertical-align: middle;
}



/* Sticky for Student ID */
#student_table_midterm th:nth-child(2),
#student_table_midterm td:nth-child(2) {
    position: sticky;
    left: 40px; 
    background: white;
    z-index: 4;
}

/* Sticky for Student Name */
#student_table_midterm th:nth-child(3),
#student_table_midterm td:nth-child(3) {
    position: sticky;
    left: 130px;
    background: white;
    z-index: 3;
}

/* Sticky for Edit Button (if you have 4th column) */
#student_table_midterm th:nth-child(4),
#student_table_midterm td:nth-child(4) {
    position: sticky;
    left: 250px;
    background: white;
    z-index: 2;
}

</style>

</body>

</html>


==============================
old final term table
<thead>
                                    
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        
                                        <?php foreach ($finaltermCompTwo as $component): ?>
                                            <?php
                                            $componentId = $component['component_id'];
                                            $items = $componentItemsMap[$componentId]; // Use pre-fetched items
                                            $colspan = count($items);
                                            ?>
                                            <th colspan="<?= max(1, $colspan) ?>" class="text-center component-type-column">
                                                <?= ucwords($component['component_type']) ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>

                                    <!-- Subheader: Component Number & Date -->
                                    <tr>
                                        <th colspan="3"></th>
                                        <?php foreach ($finaltermCompTwo as $component): ?>
                                            <?php
                                            $componentId = $component['component_id'];
                                            $items = $componentItemsMap[$componentId]; // Use pre-fetched items
                                            if (!empty($items)) {
                                                foreach ($items as $item): ?>
                                                    <th class="text-center component-type-column">
                                                        <div style="font-size: 12px;"><?= "No. " . $item['component_no'] ?></div>
                                                        <div style="font-size: 12px; color: gray;">
                                                            <?= date('M d, Y', strtotime($item['component_date'])) ?>
                                                        </div>
                                                    </th>
                                                <?php endforeach;
                                            } else { ?>
                                                <th class="text-center component-type-column">-</th>
                                            <?php } ?>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $counter = 1;
                                    foreach ($studentList as $student): ?>
                                        <tr>
                                            <td><?= $counter ?></td>
                                            <td>
                                                <a
                                                    href="./edit_grades?faculty_sub_id=<?= $selected_faculty_sub_id ?>&grades_id=<?= $student['grades_id'] ?>&active_period=<?= $_GET['active_period'] ?? 'midterm' ?>">
                                                    <?= $student['student_id'] ?>
                                                </a>
                                            </td>
                                            <td><?= ucwords($student['fullName']) ?></td>
                                           

                                            <?php foreach ($finaltermCompTwo as $component): ?>
                                                <?php
                                                $componentId = $component['component_id'];
                                                $items = $componentItemsMap[$componentId]; // Use pre-fetched items
                                                if (!empty($items)) {
                                                    foreach ($items as $item):
                                                        $scoreData = $scores->getAllScoreByItemStud($student['grades_id'], $item['items_id']);
                                                        if (!empty($scoreData)) {
                                                            foreach ($scoreData as $score): ?>
                                                               <td class="text-center">
                                            <span class="score-text" style="color:#952323;">
                                                <?= htmlspecialchars($score['score']) ?>
                                            </span>
                                            <input type="text"
                                                   class="score-input"
                                                   name="scores[<?= $student['grades_id'] ?>][<?= $item['items_id'] ?>]"
                                                   value="<?= htmlspecialchars($score['score']) ?>"
                                                   style="width: 40px; text-align: center; color:#952323; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; display: none;" />
                                            <span class="text-secondary">/<?= $item['component_quantity'] ?></span>
                                        </td>
                                                            <?php endforeach;
                                                        } else { ?>
                                                                <td class="text-center">
                                        <span class="score-text" style="color:#952323;">0</span>
                                        <input type="text"
                                               class="score-input"
                                               name="scores[<?= $student['grades_id'] ?>][<?= $item['items_id'] ?>]"
                                               value="0"
                                               style="width: 40px; text-align: center; color:#952323; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; display: none;" />
                                        <span class="text-secondary">/<?= $item['component_quantity'] ?></span>
                                    </td>
                                                        <?php }
                                                    endforeach;
                                                } else { ?>
                                                    <td class="text-center">-</td>
                                                <?php } ?>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php
                                        $counter++;
                                    endforeach;
                                    ?>
                                </tbody>