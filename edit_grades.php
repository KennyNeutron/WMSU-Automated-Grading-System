<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header('location: ./login.php');
    exit();
}

require_once './classes/students.class.php';
require_once './classes/component.class.php';
require_once './classes/component_items.class.php';
require_once './classes/component_scores.class.php';
require_once './classes/faculty_subs.class.php';
require_once './classes/grades.class.php';
require_once './classes/period.class.php';
require_once './classes/point_equivalent.class.php';

$students = new Students();
$components = new SubjectComponents();
$comp_items = new ComponentItems();
$scores = new ComponentScores();
$fac_subs = new Faculty_Subjects();
$period = new Periods();
$grades = new Grades();
$pointEqv = new PointEqv();

$faculty_sub_id = $_GET['faculty_sub_id'] ?? null;
$grades_id = $_GET['grades_id'] ?? null;
$active_period = $_GET['active_period'] ?? null;
$grade_period = '';

if ($active_period === 'midterm') {
    $grade_period = 'Midterm';
} else {
    $grade_period = 'Final Term';
}

$subject = $fac_subs->getProf($faculty_sub_id);
$gradeEquivalents = $pointEqv->getByFacultySubject($faculty_sub_id);
$student = $grades->showById($grades_id);
$gradingComponents = ($active_period === 'finalterm') ? $period->showFinalterm($faculty_sub_id) : $period->showMidterm($faculty_sub_id);
$midtermGrade = $period->showMidterm($faculty_sub_id);
$error_message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_grades'])) {
        foreach ($_POST['grades'] as $component_id => $items) {
            foreach ($items as $items_id => $score) {
                $score = is_numeric($score) ? floatval($score) : 0;

                if ($scores->scoreExists($grades_id, $items_id)) {
                    $scores->updateScore($grades_id, $items_id, $score);
                } else {
                    $scores->add($grades_id, $items_id, $score);
                }
            }
        }
        $message = 'Grades saved successfully!';
        $success = true;
    } elseif (isset($_POST['post_grades'])) {
        $avgGrade = 0;
        foreach ($gradingComponents as $component) {
            $avgGrade += $scores->calculateWeightByComponent($grades_id, $component['component_id']) ?: 0;
        }
        $avgGrade = round($avgGrade, 2);

        $midtermAvg = 0;
        if ($active_period !== 'midterm') {
            foreach ($midtermGrade as $component) {
                $midtermAvg += $scores->calculateWeightByComponent($grades_id, $component['component_id']) ?: 0;
            }
            $midtermAvg = round($midtermAvg, 2);
        }

        if ($active_period === 'midterm') {
            $grades->updateMidtermGrade($grades_id, $avgGrade);
        } else {
            $grades->updateFinalGrade($grades_id, $avgGrade, $midtermAvg);
        }

        $message = 'Grade posted successfully!';
        $success = true;
    } elseif (isset($_POST['mark_inc'])) {
        if ($active_period === 'midterm') {
            $grades->updateMidtermGrade($grades_id, 'INC');
        } else {
            $grades->updateFinalGrade($grades_id, 'INC', 'INC');
        }

        $message = 'Student marked as INC successfully!';
        $success = true;
    }
}




?>



<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Edit Grades';
$home_page = 'active';
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
                    <a href="./subject_students.php?faculty_sub_id=<?= $faculty_sub_id ?>" class="bg-none"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></a>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 h1 m-0">Edit Grades</span>
                    </div>
                </div>
            </div>

            <div class="mx-5 my-3 position-relative">
                <form action="#" method="post">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex flex-column align-items-center">
                        <h3 class="brand-color"><?= ucwords($subject['sub_name']) ?></h3>
                        <h4><?= $subject['sub_code'] ?> (<?= $subject['yr_sec'] ?>)</h4>
                    </div>

                    <h3>Student Information</h3>
                    <div class="row row-cols-1 row-cols-md-2">
                        <div class="col">
                            <div class="mb-3 ms-5">
                                <label class="form-label">Student ID</label>
                                <input type="text" class="form-control"
                                    value="<?= htmlspecialchars($student['student_id']) ?>" readonly>
                            </div>
                            <div class="mb-3 ms-5">
                                <label class="form-label">Student Name</label>
                                <input type="text" class="form-control"
                                    value="<?= htmlspecialchars($student['fullName']) ?>" readonly>
                            </div>
                        </div>

                        <div class="col">
                            <div class="mb-3 ms-5">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control"
                                    value="<?= htmlspecialchars($student['email']) ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <hr style="color:#952323; padding: 1px;">

                    <?php foreach ($gradingComponents as $index => $component): ?>
                        <div class="component-container">
                            <h4 class="my-4 text-primary"><?= htmlspecialchars($component['component_type']) ?>
                                (<?= htmlspecialchars($component['weight']) ?>%)</h4>

                            <?php
                            $component_items = $comp_items->getItemById($component['component_id']);
                            foreach ($component_items as $item):
                                // $current_grade = $grades->getGrade($student['student_id'], $component['component_id'], $item['component_no']);
                                ?>
                                <div class="row mb-4">
                                    <div class="col-md-1">
                                        <p class="title_page">
                                            <?= $component['component_type'] . ' No.' . $item['component_no'] ?>
                                        </p>
                                        <p>(<?= date('M d, Y', strtotime($item['component_date'])) ?>)</p>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Total</label>
                                        <input type="number" style="width: 120px;" id="total" class="form-control"
                                            value="<?= htmlspecialchars($item['component_quantity']) ?>" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Score</label>
                                        <input type="number" style="width: 120px;" id="score" class="form-control score-input"
                                            name="grades[<?= $component['component_id'] ?>][<?= $item['items_id'] ?>]"
                                            value="<?= htmlspecialchars($scores->getScoreByItemStud($grades_id, $item['items_id']) ?: 0) ?>"
                                            data-total="<?= $item['component_quantity'] ?>">
                                    </div>
                                </div>

                            <?php endforeach; ?>

                            <div class="row mt-5">
                                <div class="col-md-1">
                                    <label class="form-label">Average</label>
                                    <input type="text" style="width: 120px;" class="form-control average-score"
                                        value="<?= round($scores->calculateAverageByComponent($grades_id, $component['component_id']) ?: 0, 2) ?>%"
                                        readonly>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Weight</label>
                                    <input type="text" style="width: 120px;" class="form-control weighted-score"
                                        value="<?= $_POST['weighted_score'] ?? round($scores->calculateWeightByComponent($grades_id, $component['component_id']) ?: 0, 2) ?>%"
                                        readonly>
                                </div>
                            </div>

                        </div>
                        <hr class="mb-5">
                    <?php endforeach; ?>
                    <div style="margin-bottom: 10rem;"></div>
                    <div class="d-flex justify-content-between align-items-center mt-4 gap-2 sticky-btn">
                        <div class="d-flex flex-row gap-3 ms-5">
                            <?php
                            $avgGrade = 0;
                            $midtermAvg = 0;
                            foreach ($gradingComponents as $component) {
                                $avgGrade += $scores->calculateWeightByComponent($grades_id, $component['component_id']) ?: 0;
                            }
                            foreach ($midtermGrade as $component) {
                                $midtermAvg += $scores->calculateWeightByComponent($grades_id, $component['component_id']) ?: 0;
                            }
                            $avgGrade = round($avgGrade, 2);
                            $midtermAvg = round($midtermAvg, 2);

                            function getNumericalRating($grade, $gradeEquivalents)
                            {
                                if (!empty($gradeEquivalents)) {
                                    if ($grade >= $gradeEquivalents['1_00'])
                                        return 1.00;
                                    elseif ($grade >= $gradeEquivalents['1_25'])
                                        return 1.25;
                                    elseif ($grade >= $gradeEquivalents['1_50'])
                                        return 1.50;
                                    elseif ($grade >= $gradeEquivalents['1_75'])
                                        return 1.75;
                                    elseif ($grade >= $gradeEquivalents['2_00'])
                                        return 2.00;
                                    elseif ($grade >= $gradeEquivalents['2_25'])
                                        return 2.25;
                                    elseif ($grade >= $gradeEquivalents['2_50'])
                                        return 2.50;
                                    elseif ($grade >= $gradeEquivalents['2_75'])
                                        return 2.75;
                                    elseif ($grade >= $gradeEquivalents['3_00'])
                                        return 3.00;
                                    else
                                        return 5.00;
                                }
                            }

                            $numericalRating = getNumericalRating(($midtermAvg + $avgGrade) / 2, $gradeEquivalents);
                            ?>
                            <?php if ($active_period !== 'midterm'): ?>
                                <!-- <div>
                                    <label class="form-label" style="color: #952323;">Midterm Grade</label>
                                    <input type="text" style="width: 120px;" class="form-control average-score"
                                        value="<?= $midtermAvg ?>%" readonly>
                                </div> -->
                                <div>
                                    <label class="form-label" style="color: #952323;"><?= $grade_period ?> Grade</label>
                                    <input type="text" style="width: 120px;" class="form-control average-score"
                                        value="<?= $avgGrade ?>%" readonly>
                                </div>
                                <div style="margin-left: 3em;">
                                    <label class="form-label" style="color: #952323;">Point Eqv.(Expected)</label>
                                    <input type="text" style="width: 120px;" class="form-control weighted-score"
                                        value="<?= number_format((float) $numericalRating, 2, '.', '') ?>" readonly>
                                </div>


                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn brand-bg-color" id="markBtn">Mark as INC</button>
                                    <button type="button" class="btn brand-bg-color" id="postBtn"><i
                                            class='bx bx-upload'></i> Apply Grade</button>
                                </div>
                            <?php endif; ?>

                            <?php if ($active_period == 'midterm'): ?>
                                <div>
                                    <label class="form-label" style="color: #952323;">Midterm Grade</label>
                                    <input type="text" style="width: 120px;" class="form-control average-score"
                                        value="<?= $midtermAvg ?>%" readonly>
                                </div>
                                <!-- <div>
                                    <label class="form-label" style="color: #952323;"><?= $grade_period ?> Grade</label>
                                    <input type="text" style="width: 120px;" class="form-control average-score"
                                        value="<?= $avgGrade ?>%" readonly>
                                </div> -->
                                <div style="margin-left: 3em;">
                                    <label class="form-label" style="color: #952323;">Point Eqv.(Expected)</label>
                                    <input type="text" style="width: 120px;" class="form-control weighted-score"
                                        value="<?= number_format((float) $numericalRating, 2, '.', '') ?>" readonly>
                                </div>


                              
                            <?php endif; ?>
                        </div>

                        <div>
                            <a href="subject_students.php?faculty_sub_id=<?= $faculty_sub_id ?>" type="button"
                                class="btn btn-secondary">Cancel</a>
                            <button type="button" class="btn brand-bg-color" id="saveChangesBtn"><i
                                    class='bx bxs-save me-2'></i>Save
                                Changes</button>
                        </div>
                    </div>

                    <div class="modal fade" id="saveConfirmationModal" tabindex="-1"
                        aria-labelledby="saveConfirmationModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="saveConfirmationModalLabel">Confirm Save</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to save these changes?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="edit_grades" id="edit_grades"
                                        class="btn btn-primary">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($active_period !== 'midterm'): ?>
                        <div class="modal fade" id="markConfirmationModal" tabindex="-1"
                            aria-labelledby="markConfirmationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="markConfirmationModalLabel">Confirm Save</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to mark this student's grade as Incomplete (INC)? <span class="text-danger"> This action cannot be undone.</span>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="mark_inc" id="mark_inc"
                                            class="btn btn-primary">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="postConfirmationModal" tabindex="-1"
                            aria-labelledby="postConfirmationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="postConfirmationModalLabel">Confirm Save</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to apply this grade?<span class="text-danger"> This action cannot be undone.</span>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="post_grades" id="post_grades"
                                            class="btn btn-primary">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".score-input").forEach(input => {
                input.addEventListener("input", function () {
                    let maxScore = parseFloat(this.dataset.total) || 0;
                    let enteredScore = parseFloat(this.value) || 0;

                    if (enteredScore > maxScore) {
                        this.value = maxScore;
                        alert(`Score cannot exceed ${maxScore}`);
                    } else if (enteredScore < 0) {
                        this.value = 0;
                        alert("Score cannot be less than 0");
                    }

                    const componentContainer = this.closest(".component-container");
                    updateComponentCalculations(componentContainer);
                });
            });
        });

        document.getElementById('saveChangesBtn').addEventListener('click', function () {
            let saveModal = new bootstrap.Modal(document.getElementById('saveConfirmationModal'));
            saveModal.show();
        });

        document.getElementById('markBtn').addEventListener('click', function () {
            let saveModal = new bootstrap.Modal(document.getElementById('markConfirmationModal'));
            saveModal.show();
        });

        document.getElementById('postBtn').addEventListener('click', function () {
            let saveModal = new bootstrap.Modal(document.getElementById('postConfirmationModal'));
            saveModal.show();
        });

        function updateComponentCalculations(componentContainer) {
            let totalScore = 0, totalMaxScore = 0, count = 0;

            componentContainer.querySelectorAll("#score").forEach(input => {
                let score = parseFloat(input.value) || 0;
                let maxScore = parseFloat(input.closest(".row").querySelector("#total").value) || 1;

                if (score > maxScore) {
                    input.value = maxScore;
                    alert(`Score cannot exceed ${maxScore}`);
                } else if (score < 0) {
                    input.value = 0;
                    alert("Score cannot be less than 0");
                }

                totalScore += score;
                totalMaxScore += maxScore;
                count++;
            });

            let avgScore = count > 0 ? (totalScore / totalMaxScore) * 100 : 0;
            let weight = parseFloat(componentContainer.dataset.weight) || 0;
            let weightedScore = (avgScore / 100) * weight;

            componentContainer.querySelector(".average-score").value = avgScore.toFixed(2);
            componentContainer.querySelector(".weighted-score").value = weightedScore.toFixed(2) + "%";
        }


        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = './edit_grades?faculty_sub_id=<?= $faculty_sub_id ?>&grades_id=<?= $grades_id ?>&active_period=<?= $active_period ?>';
            }, 1500);
        <?php endif; ?>
    </script>



<script>
    
</script>
</body>

</html>