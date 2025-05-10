<?php 

require_once 'database.php';

class Year_lvl {
  //attributes

  public $year_level_id;
  public $year_level;

  protected $db;

  function __construct() {
    $this->db = new Database();
  }

  
  function show() {
    $sql = "SELECT * FROM year_lvl ORDER BY year_level ASC;";
    $query = $this->db->connect()->prepare($sql);
    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }

  function getYearLvlById($year_level_id) {
    $sql = "SELECT year_level FROM year_lvl WHERE year_level_id = :year_level_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':year_level_id', $year_level_id);
    $yearLvl = null;

    if ($query->execute()) {
      $result = $query->fetch(PDO::FETCH_ASSOC);
      if ($result) {
        $yearLvl['year_level'] = $result['year_level'];
      }
    }
    return $yearLvl;
  }

  function exists($year_level_id) {
    $sql = "SELECT COUNT(*) as count FROM year_lvl WHERE year_level_id = :year_level_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':year_level_id', $year_level_id);
    
    if ($query->execute()) {
      $result = $query->fetch(PDO::FETCH_ASSOC);
      return $result['count'] > 0;
    }
    
    return false;
  }
}

?>