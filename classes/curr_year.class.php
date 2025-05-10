<?php 

require_once 'database.php';

class Curr_year {
  //attributes

  public $curr_year_id;
  public $user_id;
  public $year_start;
  public $year_end;
  public $curriculum_year;

  protected $db;

  function __construct() {
    $this->db = new Database();
  }

  // function curr_year() {
  //   $sql = "SELECT * FROM curr_year WHERE curr_year_id = :curr_year_id LIMIT 1;";
  //   $query = $this->db->connect()->prepare($sql);
  //   $query->bindParam(':curr_year_id', $this->curr_year_id);

  //   if ($query->execute()) {
  //     $accountData =$query->fetch(PDO::FETCH_ASSOC);

  //     if ($accountData && password_verify($this->password, $accountData['password'])) {
  //       $this->curr_year_id = $accountData['curr_year_id'];
  //       $this->year_start = $accountData['year_start'];
  //       $this->year_end = $accountData['year_end'];
  //       $this->curriculum_year = $accountData['year_start'] . '-' . $accountData['year_end'];
  //       return true;
  //     }
  //   }

  //   return false;
  // }  
 
  //Methods

  function add() {
    $sql = "INSERT INTO curr_year (curr_year_id, year_start, year_end) VALUES
    (:curr_year_id, :year_start, :year_end);";
    
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':curr_year_id', $this->curr_year_id);
    $query->bindParam(':year_start', $this->year_start);
    $query->bindParam(':year_end', $this->year_end);

    if ($query->execute()) {
      return true;
    } else {
      return false;  // Return false in case of an error
    }
  }

  function edit(){
    $sql = "UPDATE curr_year SET year_start=:year_start, year_end=:year_end WHERE curr_year_id=:curr_year_id;";

    $query=$this->db->connect()->prepare($sql);
    $query->bindParam(':year_start', $this->year_start);
    $query->bindParam(':year_end', $this->year_end);

    $query->bindParam(':curr_year_id', $this->curr_year_id);
    
    if($query->execute()){
      return true;
    }
    else{
      return false;
    }	
  }

  function show() {
    $sql = "SELECT * FROM curr_year ORDER BY year_start ASC;";
    $query = $this->db->connect()->prepare($sql);
    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }

  function fetch($curr_year_id) {
    $sql = "SELECT * FROM curr_year WHERE curr_year_id = :curr_year_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':curr_year_id', $curr_year_id);
    if ($query->execute()) {
      $data = $query->fetch();
    }
    return $data;
  }

  function validateYear($inputYear) {
    // Get the current year
    $currentYear = date('Y');

    // Convert input year to an integer
    $inputYear = intval($inputYear);

    // Check if the input year is less than or equal to the current year
    if ($inputYear <= $currentYear) {
        return true; // Valid input
    } else {
        return false; // Invalid input
    }
  }

  function is_year_exist($year_start) {
    $sql = "SELECT * FROM curr_year WHERE year_start = :year_start;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':year_start', $year_start);
    if ($query->execute()) {
      if ($query->rowCount() > 0) {
        return true;
      }
    }
    return false;
  }
  function fetchByYearStart($year_start) {
    $sql = "SELECT * FROM curr_year WHERE year_start = :year_start;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':year_start', $year_start);
    if ($query->execute()) {
      $data = $query->fetch();
    }
    return $data;
  }
  function getYearRangeById($curr_year_id) {
    $sql = "SELECT year_start, year_end FROM curr_year WHERE curr_year_id = :curr_year_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':curr_year_id', $curr_year_id);
    $yearRange = null;

    if ($query->execute()) {
      $result = $query->fetch(PDO::FETCH_ASSOC);
      if ($result) {
        $yearRange['year_start'] = $result['year_start'];
        $yearRange['year_end'] = $result['year_end'];
      }
    }
    return $yearRange;
  }

  function searchByYearStart($keyword) {
    $sql = "SELECT * FROM curr_year WHERE year_start LIKE :keyword;";
    $query = $this->db->connect()->prepare($sql);
    $keyword = "%$keyword%";
    $query->bindParam(':keyword', $keyword);

    $data = null;
    if ($query->execute()) {
        $data = $query->fetchAll();
    }
    return $data;
  }

  function delete($curr_year_id) {
    $sql = "DELETE FROM curr_year WHERE curr_year_id = :curr_year_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':curr_year_id', $curr_year_id);
    if ($query->execute()) {
      return true;
    } else {
      return false;
    }
  }

}

?>