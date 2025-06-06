<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
    header('location: ../login');
}

require_once '../classes/college.class.php';
require_once '../tools/functions.php';

?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Manage Colleges';
$department_page = 'active';
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
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 fw-bold h1 m-0 brand-color">Manage Colleges</span>
                    </div>
                </div>
            </div>

            <div class="m-4">
                <div class="content container-fluid mw-100 border rounded shadow p-3">

                    <div class="search-keyword col-12 flex-lg-grow-0 d-flex mb-2">

                        <div class="search-keyword col-12 flex-lg-grow-0 d-flex my-2 px-2 justify-content-end">
                            <div class="input-group w-50">
                                <input type="text" name="keyword" id="keyword" placeholder="Search"
                                    class="form-control">
                                <button class="btn btn-outline-secondary brand-bg-color" type="button"><i
                                        class='bx bx-search' aria-hidden="true"></i></button>
                            </div>
                            <a href="./add_college" class="btn btn-outline-secondary btn-add ms-3 brand-bg-color"
                                type="button"><i class='bx bx-plus-circle'></i></a>
                        </div>

                    </div>

                    <?php
                    $college = new College();
                    $collegeArray = $college->showWithDept();
                    ?>
                    <table id="manage_college" class="table table-striped table-sm" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>College ID</th>
                                <th>College Name</th>
                                <th>Departments</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            foreach ($collegeArray as $item) {
                                $departmentNames = !empty($item['department_names']) ? $item['department_names'] : 'N/A';
                                ?>
                                <tr>
                                    <td><?= $counter ?></td>
                                    <td><?= $item['college_id'] ?></td>
                                    <td><?= "College of " . trim($item['college_name']) ?></td>
                                    <td>(<?= htmlspecialchars($departmentNames) ?>)</td>
                                    <td class="text-center">
                                        <a href="./edit_college?college_id=<?=  $item['college_id'] ?>"><i class='bx bx-edit text-success fs-4'></i></a>
                                            <button class=" delete-btn bg-none"
                                            data-subject-id="<?= $item['college_id'] ?>">
                                            <i class='bx bx-trash-alt text-danger fs-4'></i>
                                            </button>
                                    </td>
                                </tr>
                                <?php
                                $counter++;
                            }
                            ?>
                        </tbody>
                    </table>

                </div>

            </div>

        </main>
    </div>

    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div id="alertContainer"></div>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this College?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/main.js"></script>
    <script>
        $(document).ready(function () {
            dataTable = $("#manage_college").DataTable({
                dom: 'Brtp',
                responsive: true,
                pageLength: 10,
                buttons: [
                    {
                        remove: 'true',
                    }
                ],
                'columnDefs': [{
                    'targets': [1],
                    'orderable': false,
                }]
            });

            var table = dataTable;
            var filter = createFilter(table, [1,2,3]);

            function createFilter(table, columns) {
                var input = $('input#keyword').on("keyup", function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (
                    settings,
                    searchData,
                    index,
                    rowData,
                    counter
                ) {
                    var val = input.val().toLowerCase();

                    for (var i = 0, ien = columns.length; i < ien; i++) {
                        if (searchData[columns[i]].toLowerCase().indexOf(val) !== -1) {
                            return true;
                        }
                    }

                    return false;
                });

                return input;
            }

            $('.delete-btn').on('click', function () {
                var collegeId = $(this).data('subject-id');
                $('#confirmDeleteBtn').data('college-id', collegeId);
                $('#deleteConfirmationModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function () {
                var collegeId = $(this).data('college-id');

                $.ajax({
                    url: './delete_college.php',
                    method: 'POST',
                    data: {
                        college_id: collegeId
                    },
                    success:
                        function (response) {
                            showAlert('College deleted successfully!', 'success');
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
                ${message}
            </div>
        `;
                alertContainer.innerHTML = alertHTML;

                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 1000);
            }
        });
    </script>
</body>

</html>