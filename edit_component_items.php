<?php
session_start();

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 1)) {
    header('location: ./login.php');
    exit(); // Ensure script stops execution after redirection
}

require_once './tools/functions.php';
require_once './classes/component_items.class.php';
require_once './classes/component.class.php';

$comp_items = new ComponentItems();
$components = new SubjectComponents();

$error_message = '';
$success = false;

// Get IDs from GET parameters
$faculty_id = isset($_GET['faculty_sub_id']) ? $_GET['faculty_sub_id'] : null;
$activePeriod = isset($_GET['period']) ? $_GET['period'] : null;
$component_id = isset($_GET['component_id']) ? $_GET['component_id'] : null;
$items_id = isset($_GET['items_id']) ? $_GET['items_id'] : null;

// Fetch Component Type based on component_id
$component_type = '';
if ($component_id) {
    $componentData = $components->getComponentById($component_id);
    $component_type = $componentData ? $componentData['component_type'] : '';
}

// Retrieve the component item details to edit
$item_data = [];
if ($items_id) {
    $item_data = $comp_items->getItemId($items_id);
}

if (isset($_POST['edit_component_item'])) {
    $component_no = htmlentities($_POST['component_no']);
    $component_quantity = htmlentities($_POST['component_quantity']);
    $component_date = htmlentities($_POST['component_date']); 
    $comp_items->items_id = $items_id; 
    $comp_items->component_no = $component_no;
    $comp_items->component_quantity = $component_quantity;
    $comp_items->component_date = $component_date;

    if ($comp_items->update()) {
        $message = 'Component Item updated successfully!';
        $success = true;
    } else {
        $error_message = 'Something went wrong updating the component item.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Edit Component Item';
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
                    <button onclick="history.back()" class="bg-none">
                        <i class='bx bx-chevron-left fs-2 brand-color'></i>
                    </button>
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="fs-2 h1 m-0">Edit <?= htmlspecialchars($component_type) ?></span>
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
                            <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <div class="row row-cols-1 row-cols-md-2">
                        <div class="col">
                            <!-- Date Field: Prefilled with existing date -->
                            <div class="mb-3">
                                <label for="component_date" class="form-label">Date</label>
                                <input type="date" class="form-control" name="component_date" id="component_date"
                                    value="<?= isset($item_data['component_date']) ? htmlspecialchars($item_data['component_date']) : date('Y-m-d') ?>" required>
                            </div>

                            <!-- Component Number -->
                            <div class="mb-3">
                                <div class="input-group" style="width: 250px;">
                                    <span class="input-group-text"><?= htmlspecialchars($component_type) ?> No.</span>
                                    <input type="number" class="form-control" name="component_no" id="component_no" required
                                        value="<?= isset($item_data['component_no']) ? htmlspecialchars($item_data['component_no']) : '' ?>">
                                </div>
                            </div>

                            <!-- Total Items/Score -->
                            <div class="mb-3">
                                <label for="component_quantity" class="form-label">Total Items/Score</label>
                                <input type="number" class="form-control" name="component_quantity" id="component_quantity" required
                                    value="<?= isset($item_data['component_quantity']) ? htmlspecialchars($item_data['component_quantity']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-start mt-4 gap-2">
                        <button onclick="history.back()" type="button" class="btn btn-secondary">Cancel</button>
                        <button type="submit" name="edit_component_item" class="btn brand-bg-color">Update</button>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <script src="./js/main.js"></script>
    <script>
        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = './column_select.php?active_period=<?= $activePeriod ?>&faculty_sub_id=<?= $faculty_id ?>?>';
            }, 1500);
        <?php endif; ?>
    </script>
</body>
</html>
