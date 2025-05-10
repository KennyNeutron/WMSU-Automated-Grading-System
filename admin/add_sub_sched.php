<?php
session_start();

require_once '../tools/functions.php';
require_once '../classes/department.class.php';
require_once '../classes/curri_page.class.php';
require_once '../classes/faculty_subs.class.php';
require_once '../classes/period.class.php';
require_once '../classes/point_equivalent.class.php';

$dept = new Department();
$subs = new Curr_table();
$pointEqv = new PointEqv();
$fac_sub = new Faculty_Subjects();
$period = new Periods();
$department_arr = $dept->showName($_GET['department_id']);
$dept_name = $department_arr['department_name'];
$subjects = $subs->showAll($dept_name);
$error_message = '';
$success = false;

function convertTo12hrFormat($time24hr)
{
    $time = DateTime::createFromFormat('H:i', $time24hr);

    if ($time) {
        return $time->format('g:i A');
    }

    return false;
}

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

if (isset($_POST['add_fac_sub'])) {
    $curr_id = htmlentities($_POST['curr_id']);
    $yr_section = htmlentities($_POST['yr_section']);
    $no_students = htmlentities($_POST['no_students']);
    $lec_check = isset($_POST['lec_check']) ? 1 : 0;
    $lab_check = isset($_POST['lab_check']) ? 1 : 0;

    $lec_days = isset($_POST['lec_days']) && !empty($_POST['lec_days']) ? implode('', $_POST['lec_days']) : NULL;
    $lec_time_start = !empty($_POST['lec_time_start']) ? htmlentities($_POST['lec_time_start']) : NULL;
    $lec_time_end = !empty($_POST['lec_time_end']) ? htmlentities($_POST['lec_time_end']) : NULL;
    $lec_room = !empty($_POST['lec_room']) ? htmlentities($_POST['lec_room']) : NULL;
    $lec_units = !empty($_POST['lec']) ? htmlentities($_POST['lec']) : NULL;

    $lab_days = isset($_POST['lab_days']) && !empty($_POST['lab_days']) ? implode('', $_POST['lab_days']) : NULL;
    $lab_time_start = !empty($_POST['lab_time_start']) ? htmlentities($_POST['lab_time_start']) : NULL;
    $lab_time_end = !empty($_POST['lab_time_end']) ? htmlentities($_POST['lab_time_end']) : NULL;
    $lab_room = !empty($_POST['lab_room']) ? htmlentities($_POST['lab_room']) : NULL;
    $lab_units = !empty($_POST['lab']) ? htmlentities($_POST['lab']) : NULL;

    $lec_time_start_12hr = $lec_time_start ? convertTo12hrFormat($lec_time_start) : NULL;
    $lec_time_end_12hr = $lec_time_end ? convertTo12hrFormat($lec_time_end) : NULL;
    $lab_time_start_12hr = $lab_time_start ? convertTo12hrFormat($lab_time_start) : NULL;
    $lab_time_end_12hr = $lab_time_end ? convertTo12hrFormat($lab_time_end) : NULL;

    $fac_sub->sched_id = $_GET['sched_id'];
    $fac_sub->curr_id = $curr_id;
    $fac_sub->yr_sec = $yr_section;
    $fac_sub->no_students = $no_students;
    $fac_sub->lec_days = $lec_days;
    $fac_sub->lab_days = $lab_days;
    $fac_sub->lec_time = ($lec_time_start_12hr && $lec_time_end_12hr) ? $lec_time_start_12hr . ' - ' . $lec_time_end_12hr : NULL;
    $fac_sub->lab_time = ($lab_time_start_12hr && $lab_time_end_12hr) ? $lab_time_start_12hr . ' - ' . $lab_time_end_12hr : NULL;
    $fac_sub->lec_room = $lec_room;
    $fac_sub->lab_room = $lab_room;
    $fac_sub->lec_units = $lec_units;
    $fac_sub->lab_units = $lab_units;

    $newId = $fac_sub->add();
    if ($newId) {
        $period->faculty_sub_id = $newId;
        $period->period_type = 'Midterm';
        $period->weight = 40;
        if ($period->add()) {
            $period->faculty_sub_id = $newId;
            $period->period_type = 'Final Term';
            $period->weight = 60;
            if ($period->add()) {
                $pointEqv->faculty_sub_id = $newId;
                $pointEqv->rating_1_00 = 97;
                $pointEqv->rating_1_25 = 94;
                $pointEqv->rating_1_50 = 91;
                $pointEqv->rating_1_75 = 88;
                $pointEqv->rating_2_00 = 85;
                $pointEqv->rating_2_25 = 79;
                $pointEqv->rating_2_50 = 76;
                $pointEqv->rating_2_75 = 75;
                $pointEqv->rating_3_00 = 60;
                $pointEqv->rating_5_00 = 40;

                if ($pointEqv->add()) {
                    $message = 'Subject added';
                    $success = true;
                } else {
                    $error_message = 'Something went wrong adding setting grade equivalents';
                }
            } else {
                $error_message = 'Something went wrong adding the Final Term period.';
            }
        } else {
            $error_message = 'Something went wrong adding the Midterm period.';
        }
    } else {
        $error_message = 'Something went wrong adding subject.';
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Add Subject';
$faculty_page = 'active';
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
            <div class="header">
                <?php
                require_once('../includes/admin_header.php')
                    ?>
            </div>

            <div class="flex-md-nowrap p-1 title_page shadow" style="background-color: whitesmoke;">
                <div class="d-flex align-items-center">
                    <button onclick="history.back()" class="bg-none d-flex align-items-center"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></button>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Add Subject</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <form action="#" method="post" onsubmit="return validateCheckboxSelection();">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success gap-2">
                            <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?> successfully!
                        </div>
                    <?php endif; ?>
                    <div class="row row-cols-1 row-cols-md-2">
                        <div class="col">
                            <input type="hidden" id="faculty_sub_id" name="faculty_sub_id">
                            <div class="mb-3">
                                <label for="curr_id" class="form-label">Select Subject</label>
                                <input type="hidden" id="curr_id" name="curr_id" value="">
                                <select class="form-select select2" id="curr_id" name="curr_id"
                                    onchange="populateDetails(this)" required>
                                    <option value="" disabled selected>Select Subject</option>
                                    <?php
                                    foreach ($subjects as $item) {
                                        $sub_id = $item['sub_code'] . ' - ' . $item['sub_name'];
                                        echo "<option value='" . htmlspecialchars($item['curr_id']) . "' 
                                                data-subname='" . htmlspecialchars($item['sub_name']) . "' 
                                                data-prereq='" . htmlspecialchars($item['sub_prerequisite']) . "' 
                                                data-lec='" . htmlspecialchars($item['lec']) . "' 
                                                data-lab='" . htmlspecialchars($item['lab']) . "' >
                                                $sub_id
                                            </option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="yr_section" class="form-label">Year/Section</label>
                                <input type="text" class="form-control" id="yr_section" name="yr_section"
                                    placeholder="eg. BSCE 2A" required>
                            </div>

                            <div class="mb-3">
                                <label for="no_students" class="form-label">No. of Students</label>
                                <input type="number" class="form-control" id="no_students" name="no_students" required>
                            </div>
                        </div>

                        <div class="col">
                            <div class="mb-3">
                                <label for="prereq" class="form-label">Prerequisite/s</label>
                                <input type="text" class="form-control" id="prereq" name="prereq" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="row row-cols-1 row-cols-md-2">
                        <div class="col">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="lec_check" name="lec_check"
                                    onchange="toggleLecFields()">
                                <label class="form-check-label" for="lec_check" class="form-label">Lecture</label>
                            </div>
                            <div id="lec_fields" style="display:none;">
                                <div class="mb-3">
                                    <label for="lec" class="form-label">Lecture Units</label>
                                    <input type="number" class="form-control" id="lec" name="lec" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="lec_room" class="form-label">Lecture Room</label>
                                    <input type="text" class="form-control" id="lec_room" placeholder="eg. CLA 2"
                                        name="lec_room">
                                </div>

                                <div class="mb-3">
                                    <label for="lec_schedule" class="form-label">Lecture Schedule</label>

                                    <div class="row">
                                        <div class="col">
                                            <div class="input-group">
                                                <label for="lec_days" class="form-text">Days</label>
                                                <select class="form-select" id="lec_days" name="lec_days[]" multiple>
                                                    <option value="M">Monday</option>
                                                    <option value="T">Tuesday</option>
                                                    <option value="W">Wednesday</option>
                                                    <option value="TH">Thursday</option>
                                                    <option value="F">Friday</option>
                                                    <option value="S">Saturday</option>
                                                    <option value="Sun">Sunday</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="lec_time" class="form-text"> Time </label>
                                            <div class="input-group">
                                                <input type="time" class="form-control" id="lec_time_start"
                                                    name="lec_time_start">
                                                <span class="input-group-text">-</span>
                                                <input type="time" class="form-control" id="lec_time_end"
                                                    name="lec_time_end">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="lab_check" name="lab_check"
                                    onchange="toggleLabFields()">
                                <label class="form-check-label" for="lab_check" class="form-label">Laboratory</label>
                            </div>
                            <div id="lab_fields" style="display:none;">
                                <div class="mb-3">
                                    <label for="lab" class="form-label">Laboratory Units</label>
                                    <input type="number" class="form-control" id="lab" name="lab" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="lab_room" class="form-label">Laboratory Room</label>
                                    <input type="text" class="form-control" id="lab_room" placeholder="eg. CLA 21"
                                        name="lab_room">
                                </div>
                                <div class="mb-3">
                                    <label for="lec_schedule" class="form-label">Laboratory Schedule</label>

                                    <div class="row">
                                        <div class="col">
                                            <div class="input-group">
                                                <label for="lab_days" class="form-text">Days</label>
                                                <select class="form-select" id="lab_days" name="lab_days[]" multiple>
                                                    <option value="M">Monday</option>
                                                    <option value="T">Tuesday</option>
                                                    <option value="W">Wednesday</option>
                                                    <option value="TH">Thursday</option>
                                                    <option value="F">Friday</option>
                                                    <option value="S">Saturday</option>
                                                    <option value="Sun">Sunday</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="lab_time" class="form-text"> Time </label>
                                            <div class="input-group">
                                                <input type="time" class="form-control" id="lab_time_start"
                                                    name="lab_time_start">
                                                <span class="input-group-text">-</span>
                                                <input type="time" class="form-control" id="lab_time_end"
                                                    name="lab_time_end">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="d-flex justify-content-end gap-2">
                        <a onclick="history.back()" class="btn btn-secondary">Cancel</a>
                        <button type="submit" name="add_fac_sub" class="btn brand-bg-color">Submit</button>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: "Select Subject",
                allowClear: true
            });
        });

        new MultiSelectTag('lec_days', {
            rounded: true,    // default true
            shadow: true,      // default false
            tagColor: {
                textColor: '#952323',
                borderColor: '#dd3838',
                bgColor: '#e59d9d',
            },
            onChange: function (values) {
                console.log(values)
            }
        })

        new MultiSelectTag('lab_days', {
            rounded: true,    // default true
            shadow: true,      // default false
            tagColor: {
                textColor: '#952323',
                borderColor: '#dd3838',
                bgColor: '#e59d9d',
            },
            onChange: function (values) {
                console.log(values)
            }
        })

        function capitalizeFirstLetter(input) {
            input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
        }

        function toggleLecFields() {
            const lecChecked = document.getElementById('lec_check').checked;
            const lecFields = document.getElementById('lec_fields');
            const lecRoom = document.getElementById('lec_room');
            const lecSchedule = document.getElementById('lec_schedule');
            const lecDays = document.getElementById('lec_days');
            const lecTime = document.getElementById('lec_time');
            const lecUnits = document.getElementById('lec_units');

            // Show/Hide fields based on checkbox state
            lecFields.style.display = lecChecked ? 'block' : 'none';
            lecRoom.disabled = !lecChecked;
            lecSchedule.disabled = !lecChecked;
            lecDays.disabled = !lecChecked;
            lecTime.disabled = !lecChecked;
            lecUnits.disabled = !lecChecked;

            // Update units if the subject is selected
            if (lecChecked) {
                updateUnits('lec');
            }
        }

        function toggleLabFields() {
            const labChecked = document.getElementById('lab_check').checked;
            const labFields = document.getElementById('lab_fields');
            const labRoom = document.getElementById('lab_room');
            const labSchedule = document.getElementById('lab_schedule');
            const labDays = document.getElementById('lab_days');
            const labTime = document.getElementById('lab_time');
            const labUnits = document.getElementById('lab_units');

            // Show/Hide fields based on checkbox state
            labFields.style.display = labChecked ? 'block' : 'none';
            labRoom.disabled = !labChecked;
            labSchedule.disabled = !labChecked;
            labDays.disabled = !labChecked;
            labTime.disabled = !labChecked;
            labUnits.disabled = !labChecked;

            // Update units if the subject is selected
            if (labChecked) {
                updateUnits('lab');
            }
        }

        function populateDetails(select) {
            const selectedOption = select.options[select.selectedIndex];

            if (!selectedOption) return;

            const subname = selectedOption.getAttribute('data-subname');
            const prereq = selectedOption.getAttribute('data-prereq');
            const lec = selectedOption.getAttribute('data-lec');
            const lab = selectedOption.getAttribute('data-lab');
            const subId = selectedOption.value;

            // Populate input fields
            document.getElementById('prereq').value = prereq || '';
            document.getElementById('lec').value = lec || '';
            document.getElementById('lab').value = lab || '';
        }
        function validateCheckboxSelection() {
    const lecChecked = document.getElementById('lec_check').checked;
    const labChecked = document.getElementById('lab_check').checked;

    if (!lecChecked && !labChecked) {
        alert("Please select at least Lecture or Lab.");
        return false; // prevent form submission
    }

    return true; // allow form submission
}

        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = './faculty_schedule.php?sched_id=<?= $_GET['sched_id'] ?>&department_id=<?= $_GET['department_id'] ?>';
            }, 1500);
        <?php endif; ?>
    </script>

</body>

</html>