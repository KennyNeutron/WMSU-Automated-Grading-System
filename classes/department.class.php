<?php

require_once 'database.php';

class Department
{

  public $department_id;
  public $department_name;

  protected $db;

  function __construct()
  {
    $this->db = new Database();
  }

  function add($department_name)
  {
    $sql = "INSERT INTO college_department_table (department_name) VALUES 
    (:department_name);";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':department_name', $department_name);

    if ($query->execute()) {
      return true;
    } else {
      return false;
    }
  }

  function show()
  {
    $sql = "SELECT * FROM college_department_table ORDER BY department_id ASC;";

    $query = $this->db->connect()->prepare($sql);
    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }

  function showName($department_id)
  {
    $sql = "SELECT department_name FROM college_department_table WHERE department_id = :department_id;";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':department_id', $department_id);
    if ($query->execute()) {
      $data = $query->fetch();
    }
    return $data;
  }

  function searchByDeptName($keyword)
  {
    $sql = "SELECT * FROM college_department_table WHERE department_name LIKE :keyword;";
    $query = $this->db->connect()->prepare($sql);
    $keyword = "%$keyword%";
    $query->bindParam(':keyword', $keyword);

    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }
  public function delete($department_id)
  {
    $query = "DELETE FROM college_department_table WHERE department_id = :department_id";
    $stmt = $this->db->connect()->prepare($query);
    $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);

    return $stmt->execute();
  }
}

?>