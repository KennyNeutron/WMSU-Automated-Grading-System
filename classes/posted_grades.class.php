<?php

require_once 'database.php';

class PostedGrades
{
    public $posted_grades_id;
    public $emp_id;
    public $student_data_id;
    public $faculty_sub_id;
    public $point_eqv;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function add()
    {
        $sql = "INSERT INTO posted_grades (emp_id, student_data_id, faculty_sub_id, point_eqv) 
                VALUES (:emp_id, :student_data_id, :faculty_sub_id, :point_eqv)";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':emp_id', $this->emp_id);
        $query->bindParam(':student_data_id', $this->student_data_id);
        $query->bindParam(':faculty_sub_id', $this->faculty_sub_id);
        $query->bindParam(':point_eqv', $this->point_eqv);

        return $query->execute();
    }

    function show()
    {
        $sql = "SELECT * FROM posted_grades ORDER BY posted_grades_id DESC";

        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    function showByCourse($course_id, $curr_year_id)
    {
        $sql = "SELECT pg.*, fs.curr_id, ct.college_course_id, ct.curr_year_id, 
               p.f_name, p.l_name, p.email, 
               ct.sub_code, ct.sub_name
        FROM curr_table ct
        INNER JOIN faculty_subjects fs ON ct.curr_id = fs.curr_id
        INNER JOIN posted_grades pg ON fs.faculty_sub_id = pg.faculty_sub_id
        INNER JOIN profiling_table p ON pg.emp_id = p.emp_id
        WHERE ct.college_course_id = :course_id
        AND ct.curr_year_id = :curr_year_id
        GROUP BY pg.emp_id, fs.curr_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':course_id', $course_id);
        $query->bindParam(':curr_year_id', $curr_year_id);
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    function getByFacSub($emp_id, $faculty_sub_id)
    {
        $sql = "SELECT pg.*, p.f_name, p.l_name, p.email, 
               sd.student_id, sd.student_firstname as student_first, sd.student_lastname as student_last
        FROM posted_grades pg
        INNER JOIN students sd ON pg.student_data_id = sd.student_data_id
        INNER JOIN profiling_table p ON pg.emp_id = p.emp_id
        WHERE pg.faculty_sub_id = :faculty_sub_id
        AND pg.emp_id = :emp_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $faculty_sub_id);
        $query->bindParam(':emp_id', $emp_id);
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    function getById($posted_grades_id)
    {
        $sql = "SELECT * FROM posted_grades WHERE posted_grades_id = :posted_grades_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':posted_grades_id', $posted_grades_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }

    function searchByPointEqv($keyword)
    {
        $sql = "SELECT * FROM posted_grades WHERE point_eqv LIKE :keyword";
        $query = $this->db->connect()->prepare($sql);
        $keyword = "%$keyword%";
        $query->bindParam(':keyword', $keyword);

        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    public function edit()
    {
        $sql = "UPDATE posted_grades SET
                    emp_id = :emp_id,
                    student_data_id = :student_data_id,
                    faculty_sub_id = :faculty_sub_id,
                    point_eqv = :point_eqv
                WHERE posted_grades_id = :posted_grades_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':posted_grades_id', $this->posted_grades_id);
        $query->bindParam(':emp_id', $this->emp_id);
        $query->bindParam(':student_data_id', $this->student_data_id);
        $query->bindParam(':faculty_sub_id', $this->faculty_sub_id);
        $query->bindParam(':point_eqv', $this->point_eqv);

        return $query->execute();
    }

    public function delete($posted_grades_id)
    {
        $sql = "DELETE FROM posted_grades WHERE posted_grades_id = :posted_grades_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':posted_grades_id', $posted_grades_id);

        return $stmt->execute();
    }
}
?>