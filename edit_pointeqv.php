<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
    header('location: ./login.php');
    exit(); // Make sure to exit after redirection
}

require_once './classes/point_equivalent.class.php';
require_once './classes/faculty_subs.class.php';

$pointEqv = new PointEqv();
$facultySubs = new Faculty_Subjects();

$faculty_sub_id = $_GET['faculty_sub_id'] ?? null;
$period = $_GET['period'] ?? 'midterm';

$subject = $facultySubs->getProf($faculty_sub_id);
$equivalents = $pointEqv->getByFacultySubject($faculty_sub_id);
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Edit Grade Equivalents';
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
                    <button onclick="history.back()" class="bg-none"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></button>

                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Edit Grade
                            Equivalents</span>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column align-items-center">
                <h3 class="brand-color"><?= $subject ? ucwords($subject['sub_name']) : '' ?></h3>
                <h4><?= $subject ? $subject['sub_code'] : '' ?> <?= $subject ? '(' . $subject['yr_sec'] . ')' : '' ?>
                </h4>
            </div>

            <div class="m-4 d-flex justify-content-center">
                <table class="table table-striped cell-border" style="width:50%">
                    <thead>
                        <tr>
                            <th class="text-center" scope="col">Grade Equivalent</th>
                            <th class="text-center" scope="col">Numerical Rating</th>
                            <th class="text-center" scope="col" width="20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $gradeKeys = [
                            '1_00' => '1.00',
                            '1_25' => '1.25',
                            '1_50' => '1.50',
                            '1_75' => '1.75',
                            '2_00' => '2.00',
                            '2_25' => '2.25',
                            '2_50' => '2.50',
                            '2_75' => '2.75',
                            '3_00' => '3.00',
                            '5_00' => '5.00'
                        ];
                        foreach ($gradeKeys as $key => $gradeDisplay) {
                            if (isset($equivalents[$key])) {
                                ?>
                                <tr>
                                    <td class="text-center"><?= $gradeDisplay ?></td>
                                    <td class="text-center"><?= $equivalents[$key] ?> <span style="color: gray;">and
                                            above</span></td>
                                    <td class="text-center">
                                        <button class="btn btn-link edit-btn bg-none p-0" data-bs-toggle="modal"
                                            data-bs-target="#editNumericalModal" data-faculty-sub-id="<?= $faculty_sub_id ?>"
                                            data-point-eqv-id="<?= $equivalents['point_eqv_id'] ?>" data-grade-key="<?= $key ?>"
                                            data-grade-display="<?= $gradeDisplay ?>"
                                            data-numerical-rating="<?= $equivalents[$key] ?>">
                                            <i class='bx bx-edit text-success fs-4'></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div class="modal fade" id="editNumericalModal" tabindex="-1" aria-labelledby="editNumericalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNumericalModalLabel">Edit Numerical Rating</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editNumericalForm" method="post" action="./update_numerical.php">
                    <div class="modal-body">
                        <input type="hidden" name="faculty_sub_id" id="modalFacultySubId">
                        <input type="hidden" name="point_eqv_id" id="modalPointEqvId">
                        <input type="hidden" name="grade_key" id="modalGradeKey">

                        <div class="mb-3">
                            <label for="gradeEquivalent" class="form-label">Grade Equivalent</label>
                            <input type="text" class="form-control" id="gradeEquivalent" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="numericalRating" class="form-label">Numerical Rating</label>
                            <input type="number" class="form-control" id="numericalRating" name="numerical_rating"
                                min="0" max="99" required>
                            <div class="form-text">Enter a value between 0-100</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-btn');
            const editModal = new bootstrap.Modal(document.getElementById('editNumericalModal'));

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const facultySubId = this.getAttribute('data-faculty-sub-id');
                    const pointEqvId = this.getAttribute('data-point-eqv-id');
                    const gradeKey = this.getAttribute('data-grade-key');
                    const gradeDisplay = this.getAttribute('data-grade-display');
                    const numericalRating = this.getAttribute('data-numerical-rating');

                    document.getElementById('modalFacultySubId').value = facultySubId;
                    document.getElementById('modalPointEqvId').value = pointEqvId;
                    document.getElementById('modalGradeKey').value = gradeKey;
                    document.getElementById('gradeEquivalent').value = gradeDisplay;
                    document.getElementById('numericalRating').value = numericalRating;

                    editModal.show();
                });
            });

            document.getElementById('editNumericalForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Numerical rating updated successfully!');
                            editModal.hide();
                            window.location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to update numerical rating'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating the numerical rating');
                    });
            });
        });
    </script>
</body>

</html>