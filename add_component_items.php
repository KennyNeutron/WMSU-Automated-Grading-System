<?php

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
    header('location: ./login.php');
}

require_once './classes/component_items.class.php';
require_once './classes/component.class.php';

$comp_items = new ComponentItems();
$components = new SubjectComponents();

$error_message = '';
$success = false;

// Fetch Component Type based on component_id
$component_id = isset($_GET['component_id']) ? $_GET['component_id'] : null;
$component_type = '';

if ($component_id) {
    $componentData = $components->getComponentById($component_id);
    $component_type = $componentData ? $componentData['component_type'] : '';
}

if (isset($_POST['add_component_item'])) {
    $component_id = $_GET['component_id'];
    $component_no = htmlentities($_POST['component_no']);
    $component_quantity = htmlentities($_POST['component_quantity']);
    $component_date = htmlentities($_POST['component_date']); 

    $comp_items->component_id = $component_id;
    $comp_items->component_no = $component_no;
    $comp_items->component_quantity = $component_quantity;
    $comp_items->component_date = $component_date;

    if ($comp_items->add()) {
        $message = 'Component Item added';
        $success = true;
    } else {
        $error_message = 'Something went wrong adding component item.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Add Component Item';
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
                    <button onclick="history.back()" class="bg-none"><i
                            class='bx bx-chevron-left fs-2 brand-color'></i></button>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 h1 m-0">Add <?= $component_type ?></span>
                    </div>
                </div>
            </div>

            <div class="m-5 py-3">
                <form action="#" method="post">

                    <?php if (!empty($error_message)): ?>
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
                            <div class="mb-3">
                                <label for="component_date" class="form-label">Date</label>
                                <input type="date" class="form-control" name="component_date" id="component_date"
                                    value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="mb-3">
                                <div class="input-group" style="width: 250px;">
                                    <span class="input-group-text"><?= $component_type ?> No.</span>
                                    <input type="number" class="form-control"
                                        name="component_no" id="component_no" required>
                                    </div>
                            </div>

                            <div class="mb-3">
                                <label for="component_quantity" class="form-label">Total Items/Score</label>
                                <input type="number" class="form-control" name="component_quantity"
                                    id="component_quantity" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-start mt-4 gap-2">
                        <button onclick="history.back()" type="button" class="btn btn-secondary">Cancel</button>
                        <button type="submit" name="add_component_item" class="btn brand-bg-color">Add</button>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = './subject_students.php?faculty_sub_id=<?= $_GET['faculty_sub_id'] ?>';
            }, 1500);
        <?php endif; ?>
    </script>
</body>

</html>