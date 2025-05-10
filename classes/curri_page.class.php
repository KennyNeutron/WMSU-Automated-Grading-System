<?php

require_once 'database.php';

class Curr_table
{

  //attributes
  public $user_id;
  public $curr_id;
  public $curr_year_id;
  public $college_course_id;
  public $time_id;
  public $year_level_id;
  public $semester_id;
  public $sub_code;
  public $sub_name;
  public $sub_prerequisite;
  public $lec;
  public $lab;

  protected $db;

  function __construct()
  {
    $this->db = new Database();
  }

  //Methods

  function add($sub_prerequisite)
  {
    $sql = "INSERT INTO curr_table (curr_year_id, college_course_id, year_level_id, semester_id, time_id, sub_code, sub_name, sub_prerequisite, lec, lab) VALUES 
    (:curr_year_id, :college_course_id, :year_level_id, :semester_id, :time_id, :sub_code, :sub_name, :sub_prerequisite, :lec, :lab);";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':curr_year_id', $this->curr_year_id);
    $query->bindParam(':college_course_id', $this->college_course_id);
    $query->bindParam(':year_level_id', $this->year_level_id);
    $query->bindParam(':semester_id', $this->semester_id);
    $query->bindParam(':time_id', $this->time_id);
    $query->bindParam(':sub_code', $this->sub_code);
    $query->bindParam(':sub_name', $this->sub_name);
    $query->bindParam(':sub_prerequisite', $sub_prerequisite);
    $query->bindParam(':lec', $this->lec);
    $query->bindParam(':lab', $this->lab);

    if ($query->execute()) {
      return true;
    } else {
      return false;
    }
  }

  function fetch($record_curr_id)
  {
    $sql = "SELECT * FROM curr_table WHERE curr_id = :curr_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':curr_id', $record_curr_id);
    if ($query->execute()) {
      $data = $query->fetch();
    }
    return $data;
  }

  function edit()
  {
    $sql = "UPDATE curr_table SET curr_year_id=:curr_year_id, 
                                  college_course_id=:college_course_id, 
                                  year_level_id=:year_level_id, 
                                  semester_id=:semester_id,
                                  time_id=:time_id,
                                  sub_code=:sub_code,
                                  sub_name=:sub_name,
                                  sub_prerequisite=:sub_prerequisite,
                                  lec=:lec,
                                  lab=:lab
            WHERE curr_id = :curr_id;";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':curr_year_id', $this->curr_year_id);
    $query->bindParam(':college_course_id', $this->college_course_id);
    $query->bindParam(':year_level_id', $this->year_level_id);
    $query->bindParam(':semester_id', $this->semester_id);
    $query->bindParam(':time_id', $this->time_id);
    $query->bindParam(':sub_code', $this->sub_code);
    $query->bindParam(':sub_name', $this->sub_name);
    $query->bindParam(':sub_prerequisite', $this->sub_prerequisite);
    $query->bindParam(':lec', $this->lec);
    $query->bindParam(':lab', $this->lab);

    $query->bindParam(':curr_id', $this->curr_id);

    if ($query->execute()) {
      return true;
    } else {
      return false;
    }
  }

  function show($year_id, $course_id, $time_id, $year_level_id, $semester_id)
  {
    $sql = "SELECT * FROM curr_table WHERE curr_year_id = :year_id AND 
                                           college_course_id = :course_id AND 
                                           time_id = :time_id AND
                                           year_level_id = :year_level_id AND
                                           semester_id = :semester_id
            ORDER BY sub_code ASC;";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':year_id', $year_id);
    $query->bindParam(':course_id', $course_id);
    $query->bindParam(':time_id', $time_id);
    $query->bindParam(':year_level_id', $year_level_id);
    $query->bindParam(':semester_id', $semester_id);
    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }

  function showAll($dept_name)
  {
    $sql = "SELECT DISTINCT ct.*, cc.name
            FROM curr_table ct
            INNER JOIN course_curr cc ON ct.college_course_id = cc.college_course_id
            WHERE cc.name = :dept_name
            ORDER BY sub_code ASC;";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':dept_name', $dept_name);
    $data = null;

    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }



  function is_subcode_exist($sub_code)
  {
    $sql = "SELECT * FROM curr_table WHERE sub_code = :sub_code 
            AND curr_year_id = :curr_year_id 
            AND college_course_id = :college_course_id 
            AND year_level_id = :year_level_id 
            AND semester_id = :semester_id 
            AND time_id = :time_id";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':sub_code', $sub_code);
    $query->bindParam(':curr_year_id', $this->curr_year_id);
    $query->bindParam(':college_course_id', $this->college_course_id);
    $query->bindParam(':year_level_id', $this->year_level_id);
    $query->bindParam(':semester_id', $this->semester_id);
    $query->bindParam(':time_id', $this->time_id);

    if ($query->execute()) {
      if ($query->rowCount() > 0) {
        return true;
      }
    }
    return false;
  }

  function fetchSubDetailsByYearAndCourseId($course_id)
  {
    $sql = "SELECT DISTINCT sub_name, sub_code 
            FROM curr_table 
            WHERE college_course_id = :course_id
            ORDER BY sub_code ASC;";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':course_id', $course_id);

    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }

  function delete($curr_id)
  {
    $sql = "DELETE FROM curr_table WHERE curr_id = :curr_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':curr_id', $curr_id);
    if ($query->execute()) {
      return true;
    } else {
      return false;
    }
  }

  function copyCurrTableData($previousYear, $newYear)
  {
    $sql = "INSERT INTO curr_table (curr_year_id, college_course_id, year_level_id, semester_id, time_id, sub_code, sub_name, sub_prerequisite, lec, lab)
            SELECT :newYear, college_course_id, year_level_id, semester_id, time_id, sub_code, sub_name, sub_prerequisite, lec, lab
            FROM curr_table
            WHERE curr_year_id = :previousYear;";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':newYear', $newYear);
    $query->bindParam(':previousYear', $previousYear);

    if ($query->execute()) {
      // Log the number of rows copied
      $rowCount = $query->rowCount();
      error_log("Rows copied: " . $rowCount);
      return true;
    } else {
      // Log the error
      error_log("Error copying curriculum data: " . implode(" ", $query->errorInfo()));
      return false;
    }
  }
}

?>