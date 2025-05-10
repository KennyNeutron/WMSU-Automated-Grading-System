<?php

require_once 'database.php';

class Faculty_Sched
{

    public $sched_id;
    public $profiling_id;
    public $release_time;
    public $hrs_per_week;
    public $school_yr;
    public $semester;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function add($profiling_id)
    {
        $sql = "INSERT INTO faculty_schedule (profiling_id, release_time, hrs_per_week, school_yr, semester) VALUES 
    (:profiling_id, :release_time, :hrs_per_week, :school_yr, :semester);";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':profiling_id', $profiling_id);
        $query->bindParam(':release_time', $this->release_time);
        $query->bindParam(':hrs_per_week', $this->hrs_per_week);
        $query->bindParam(':school_yr', $this->school_yr);
        $query->bindParam(':semester', $this->semester);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function show1($department_id)
    {
        $sql = "
        SELECT 
            fs.*, 
            p.emp_id,
            CONCAT(p.l_name, ', ', p.f_name, ' ', p.m_name) AS fullName,
            p.email,
            p.acad_type AS academic_rank,
            p.designation,
            s.semester
        FROM faculty_schedule fs
        INNER JOIN profiling_table p ON fs.profiling_id = p.profiling_id
        LEFT JOIN semester s ON fs.semester = s.semester_id
        WHERE p.faculty_type = 'Visiting Lecturer'
        AND p.department_id = :department_id
        ORDER BY fs.school_yr ASC;
    ";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':department_id', $department_id, PDO::PARAM_INT);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    function show2($department_id)
    {
        $sql = "
        SELECT 
            fs.*, 
            p.emp_id,
            CONCAT(p.l_name, ', ', p.f_name, ' ', p.m_name) AS fullName,
            p.email,
            p.acad_type AS academic_rank,
            p.designation,
            s.semester
        FROM faculty_schedule fs
        INNER JOIN profiling_table p ON fs.profiling_id = p.profiling_id
        LEFT JOIN semester s ON fs.semester = s.semester_id
        WHERE p.faculty_type = 'Regular Lecturer'
        AND p.department_id = :department_id
        ORDER BY fs.school_yr ASC;
    ";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':department_id', $department_id, PDO::PARAM_INT);
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

    function fetch($sched_id)
    {
        $sql = "SELECT fs.*, CONCAT(p.l_name, ', ', p.f_name, ' ', p.m_name) AS fullName, s.semester as semester_name FROM faculty_schedule fs
                LEFT JOIN semester s ON fs.semester = s.semester_id
                INNER JOIN profiling_table p ON fs.profiling_id = p.profiling_id
                WHERE sched_id = :sched_id;";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':sched_id', $sched_id);
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }
    function fetchProfiling($sched_id)
    {
        $sql = "SELECT profiling_id FROM faculty_schedule WHERE sched_id = :sched_id;";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':sched_id', $sched_id);
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }

    function edit()
    {
        $sql = "UPDATE faculty_schedule SET profiling_id=:profiling_id, 
                              release_time=:release_time, 
                              hrs_per_week=:hrs_per_week,
                              school_yr=:school_yr,
                              semester=:semester
              WHERE sched_id = :sched_id;";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':sched_id', $this->sched_id);
        $query->bindParam(':profiling_id', $this->profiling_id);
        $query->bindParam(':release_time', $this->release_time);
        $query->bindParam(':hrs_per_week', $this->hrs_per_week);
        $query->bindParam(':school_yr', $this->school_yr);
        $query->bindParam(':semester', $this->semester);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function delete($sched_id)
    {
        $query = "DELETE FROM faculty_schedule WHERE sched_id = :sched_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':sched_id', $sched_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>