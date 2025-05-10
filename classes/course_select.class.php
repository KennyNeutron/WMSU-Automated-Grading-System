<?php

require_once 'database.php';

class Course_curr
{
  //attributes

  public $college_course_id;
  public $name;
  public $degree_level;

  protected $db;

  function __construct()
  {
    $this->db = new Database();
  }

  function add()
  {
    $sql = "INSERT INTO course_curr (name, degree_level) VALUES
    (:name, :degree_level);";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':name', $this->name);
    $query->bindParam(':degree_level', $this->degree_level);

    if ($query->execute()) {
      return true;
    } else {
      return false;  // Return false in case of an error
    }
  }

  function show()
  {
    $sql = "SELECT * FROM course_curr ORDER BY name ASC;";
    $query = $this->db->connect()->prepare($sql);
    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }

  public function getUniqueDegreeLevels()
  {
    $sql = "SELECT DISTINCT degree_level FROM course_curr WHERE degree_level IS NOT NULL";
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }

  public function filterByDegreeLevel($degree_level)
  {
    $sql = "SELECT * FROM course_curr WHERE degree_level = :degree_level";
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->bindParam(':degree_level', $degree_level);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function filterByDegreeLevelAndKeyword($degree_lvl, $keyword)
  {
    $degree_lvl = htmlspecialchars_decode($degree_lvl, ENT_QUOTES);
    $sql = "SELECT * FROM course_curr WHERE degree_level = :degree_level AND name LIKE :keyword";

    $query = $this->db->connect()->prepare($sql);
    $keyword = "%$keyword%";
    $query->bindParam(':degree_level', $degree_lvl, );
    $query->bindParam(':keyword', $keyword);

    if ($query->execute()) {
      return true;
    } else {
      return false;  // Return false in case of an error
    }
  }

  function getCourseNameById($college_course_id)
  {
    $sql = "SELECT * FROM course_curr WHERE college_course_id = :college_course_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':college_course_id', $college_course_id);

    if ($query->execute()) {
      $data = $query->fetch();
    }
    return $data;
  }

  function searchByCourseName($keyword)
  {
    $sql = "SELECT * FROM course_curr WHERE name LIKE :keyword;";
    $query = $this->db->connect()->prepare($sql);
    $keyword = "%$keyword%";
    $query->bindParam(':keyword', $keyword);

    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }

  public function update()
  {
    $sql = "UPDATE course_curr SET name = :name, degree_level = :degree_level WHERE college_course_id = :college_course_id";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':name', $this->name);
    $query->bindParam(':degree_level', $this->degree_level);
    $query->bindParam(':college_course_id', $this->college_course_id, PDO::PARAM_INT);
    if ($query->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function delete($college_course_id)
    {
        $query = "DELETE FROM course_curr WHERE college_course_id = :college_course_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':college_course_id', $college_course_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

  // function add() {
  //   $sql = "INSERT INTO curr_year (curr_year_id, year_start, year_end) VALUES
  //   (:curr_year_id, :year_start, :year_end);";

  //   $query = $this->db->connect()->prepare($sql);
  //   $query->bindParam(':curr_year_id', $this->curr_year_id);
  //   $query->bindParam(':year_start', $this->year_start);
  //   $query->bindParam(':year_end', $this->year_end);

  //   if ($query->execute()) {
  //     return true;
  //   } else {
  //     return false;  // Return false in case of an error
  //   }
  // }

  // function edit(){
  //   $sql = "UPDATE curr_year SET year_start=:year_start, year_end=:year_end;";

  //   $query=$this->db->connect()->prepare($sql);
  //   $query->bindParam(':blog_image', $this->year_start);
  //   $query->bindParam(':title', $this->year_end);

  //   if($query->execute()){
  //     return true;
  //   }
  //   else{
  //     return false;
  //   }	
  // }

  // function is_year_exist() {
  //   $sql = "SELECT * FROM curr_year WHERE year_start = :year_start;";
  //   $query = $this->db->connect()->prepare($sql);
  //   $query->bindParam(':year_start', $this->year_start);
  //   if ($query->execute()) {
  //     if ($query->rowCount() > 0) {
  //       return true;
  //     }
  //   }
  //   return false;
  // }
}

?>