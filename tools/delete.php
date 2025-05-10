<?php 

require_once '../tools/functions.php';
require_once '../classes/curri_page.class.php';
require_once '../classes/user.class.php';
require_once '../classes/curr_year.class.php';
require_once '../classes/profiling.class.php';

session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 2)) {
  header('location: ../login.php');
  exit;
}

if (isset($_POST['delete_subject'])) {
  $curr_table = new Curr_table();
  if ($curr_table->delete($_POST['delete_subject'])) {
    $message = 'Subject is successfully deleted.';
  } else {
    $message = 'Something went wrong deleting the subject.';
  }
  
  $redirect_url = "../admin/curri_page?year_id={$_GET['year_id']}&course_id={$_GET['course_id']}&time_id={$_GET['time_id']}&year_level_id={$_GET['year_level_id']}&semester_id={$_GET['semester_id']}";
  header("location: $redirect_url");
  exit;
}

if (isset($_POST['delete_faculty'])) {
  $profiling = new Profiling();
  if ($profiling->delete($_POST['delete_subject'])) {
    $message = 'Faculty is successfully deleted.';
  } else {
    $message = 'Something went wrong deleting the Faculty.';
  }
  
  $redirect_url = "../admin/profiling";
  header("location: $redirect_url");
  exit;
}

if (isset($_POST['delete_year'])) {
  $currYear = new Curr_year();
  if ($currYear->delete($_POST['delete_year'])) {
    $message = 'year is successfully deleted.';
  } else {
    $message = 'Something went wrong deleting the year.';
  }
  
  $redirect_url = "../admin/index";
  header("location: $redirect_url");
  exit;
}

?>
