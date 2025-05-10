<?php 

require_once 'database.php';

class Semester {
  //attributes

  public $semester_id;
  public $semester;

  protected $db;

  function __construct() {
    $this->db = new Database();
  }

  
  function show() {
    $sql = "SELECT * FROM semester ORDER BY semester_id ASC;";
    $query = $this->db->connect()->prepare($sql);
    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }

  function getSemesterById($semester_id) {
    $sql = "SELECT semester FROM semester WHERE semester_id = :semester_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':semester_id', $semester_id);
    $sem = null;

    if ($query->execute()) {
      $result = $query->fetch(PDO::FETCH_ASSOC);
      if ($result) {
        $sem['semester'] = $result['semester'];
      }
    }
    return $sem;
  }

  function exists($semester_id) {
    $sql = "SELECT COUNT(*) as count FROM semester WHERE semester_id = :semester_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':semester_id', $semester_id);
    
    if ($query->execute()) {
      $result = $query->fetch(PDO::FETCH_ASSOC);
      return $result['count'] > 0;
    }
    
    return false;
  }
}

?>