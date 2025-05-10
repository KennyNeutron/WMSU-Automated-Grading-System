<?php

require_once 'database.php';

class Profiling
{

  public $profiling_id;
  public $emp_id;
  public $f_name;
  public $l_name;
  public $m_name;
  public $email;
  public $start_service;
  public $end_service;
  public $acad_type;
  public $faculty_type;
  public $designation;
  public $department_id;

  protected $db;

  function __construct()
  {
    $this->db = new Database();
  }

  function add()
  {
    $sql = "INSERT INTO profiling_table (profiling_id, emp_id, f_name, l_name, m_name, email, start_service, end_service, acad_type, faculty_type, designation, department_id) VALUES 
    (:profiling_id, :emp_id, :f_name, :l_name, :m_name, :email, :start_service, :end_service, :acad_type, :faculty_type, :designation, :department_id);";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':profiling_id', $this->profiling_id);
    $query->bindParam(':emp_id', $this->emp_id);
    $query->bindParam(':f_name', $this->f_name);
    $query->bindParam(':l_name', $this->l_name);
    $query->bindParam(':m_name', $this->m_name);
    $query->bindParam(':email', $this->email);
    $query->bindParam(':start_service', $this->start_service);
    $query->bindParam(':end_service', $this->end_service);
    $query->bindParam(':acad_type', $this->acad_type);
    $query->bindParam(':faculty_type', $this->faculty_type);
    $query->bindParam(':designation', $this->designation);
    $query->bindParam(':department_id', $this->department_id);

    if ($query->execute()) {
      return true;
    } else {
      return false;
    }
  }

  function fetch($record_profiling_id)
  {
    $sql = "SELECT * FROM profiling_table WHERE profiling_id = :profiling_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':profiling_id', $record_profiling_id);
    if ($query->execute()) {
      $data = $query->fetch();
    }
    return $data;
  }
  function fetchEMP($emp_id)
  {
    $sql = "SELECT * FROM profiling_table WHERE emp_id = :emp_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':emp_id', $emp_id);
    if ($query->execute()) {
      $data = $query->fetch();
    }
    return $data;
  }
  public function fetchAll()
  {
    $sql = "SELECT p.*, c.department_name
            FROM profiling_table p
            LEFT JOIN college_department_table c ON p.department_id = c.department_id";
    $query = $this->db->connect()->prepare($sql);
    $data = null;

    if ($query->execute()) {
      $data = $query->fetchAll();
    }

    return $data;
  }

  public function fetchForSched($record_profiling_id)
  {
    $sql = "SELECT p.*, CONCAT(p.l_name, ', ', p.f_name, ' ', p.m_name) AS fullName, c.department_name
            FROM profiling_table p
            LEFT JOIN college_department_table c ON p.department_id = c.department_id
            WHERE profiling_id = :profiling_id;
            ";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':profiling_id', $record_profiling_id);
    $data = null;

    if ($query->execute()) {
      $data = $query->fetch();
    }

    return $data;
  }

  function edit()
  {
    $sql = "UPDATE profiling_table SET emp_id=:emp_id, 
                                       f_name=:f_name, 
                                       l_name=:l_name, 
                                       m_name=:m_name,
                                       email=:email,
                                       start_service=:start_service,
                                       end_service=:end_service,
                                       acad_type=:acad_type,
                                       faculty_type=:faculty_type,
                                       designation=:designation,
                                       department_id=:department_id
            WHERE profiling_id = :profiling_id;";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':emp_id', $this->emp_id);
    $query->bindParam(':f_name', $this->f_name);
    $query->bindParam(':l_name', $this->l_name);
    $query->bindParam(':m_name', $this->m_name);
    $query->bindParam(':email', $this->email);
    $query->bindParam(':start_service', $this->start_service);
    $query->bindParam(':end_service', $this->end_service);
    $query->bindParam(':acad_type', $this->acad_type);
    $query->bindParam(':faculty_type', $this->faculty_type);
    $query->bindParam(':designation', $this->designation);
    $query->bindParam(':department_id', $this->department_id);

    $query->bindParam(':profiling_id', $this->profiling_id);

    if ($query->execute()) {
      return true;
    } else {
      return false;
    }
  }

  function show($department_id)
  {
    if (!empty($department_id)) {
      $sql = "SELECT p.*, d.department_name FROM profiling_table p 
              JOIN college_department_table d ON p.department_id = d.department_id
              WHERE p.department_id = :department_id 
              ORDER BY p.profiling_id ASC;";

      $query = $this->db->connect()->prepare($sql);
      $query->bindParam(':department_id', $department_id);
    } else {
      $sql = "SELECT p.*, d.department_name FROM profiling_table p 
              JOIN college_department_table d ON p.department_id = d.department_id
              ORDER BY p.profiling_id ASC;";

      $query = $this->db->connect()->prepare($sql);
    }

    $data = null;
    if ($query->execute()) {
      $data = $query->fetchAll();
    }
    return $data;
  }

  function is_empId_exist($emp_id)
  {
    $sql = "SELECT * FROM profiling_table WHERE emp_id = :emp_id";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':emp_id', $emp_id);

    if ($query->execute()) {
      if ($query->rowCount() > 0) {
        return true;
      }
    }
    return false;
  }

  function is_email_exist($email)
  {
    $sql = "SELECT * FROM profiling_table WHERE email = :email";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':email', $email);

    if ($query->execute()) {
      if ($query->rowCount() > 0) {
        return true;
      }
    }
    return false;
  }

  public function get_emp_details($emp_id)
  {
    $sql = "SELECT p.designation, p.acad_type, p.faculty_type, d.department_name 
            FROM profiling_table p 
            LEFT JOIN college_department_table d ON p.department_id = d.department_id 
            WHERE p.emp_id = :emp_id";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->bindParam(':emp_id', $emp_id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }



  function delete($profiling_id)
  {
    $sql = "DELETE FROM profiling_table WHERE profiling_id = :profiling_id;";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':profiling_id', $profiling_id);
    if ($query->execute()) {
      return true;
    } else {
      return false;
    }
  }
}

?>