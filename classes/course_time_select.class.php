<?php 

require_once 'database.php';

class Curr_time {
  //attributes

  public $time_id;
  public $time_name;

  protected $db;

  function __construct() {
    $this->db = new Database();
  }

  
  function show() {
    $sql = "SELECT * FROM curr_time ORDER BY time_id ASC;";
    $query = $this->db->connect()->prepare($sql);
    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }
}

?>