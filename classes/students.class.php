<?php

require_once 'database.php';

class Students
{
    public $student_data_id;
    public $student_id;
    public $student_firstname;
    public $student_middlename;
    public $student_lastname;
    public $suffix;
    public $email;
    public $year_section;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function add()
    {
        $sql = "INSERT INTO students (student_id, student_firstname, student_middlename, student_lastname, suffix, email, year_section) VALUES
        (:student_id, :student_firstname, :student_middlename, :student_lastname, :suffix, :email, :year_section);";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':student_id', $this->student_id);
        $query->bindParam(':student_firstname', $this->student_firstname);
        $query->bindParam(':student_middlename', $this->student_middlename);
        $query->bindParam(':student_lastname', $this->student_lastname);
        $query->bindParam(':suffix', $this->suffix);
        $query->bindParam(':email', $this->email);
        $query->bindParam(':year_section', $this->year_section);

        return $query->execute();
    }

    function show()
    {
        $sql = "SELECT *, IF(suffix IS NOT NULL AND suffix != '', CONCAT(' ', suffix), '') as suffix FROM students ORDER BY student_lastname ASC;";

        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    function getStudentById($student_id)
    {
        $sql = "SELECT * FROM students WHERE student_id = :student_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':student_id', $student_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }
    function getStudentByDataId($student_id)
    {
        $sql = "SELECT * FROM students WHERE student_data_id = :student_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':student_id', $student_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }

    function searchByStudentName($keyword)
    {
        $sql = "SELECT * FROM students WHERE student_firstname LIKE :keyword OR student_lastname LIKE :keyword;";
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
        $sql = "UPDATE students SET
                    student_firstname = :firstname,
                    student_middlename = :middlename,
                    student_lastname = :lastname,
                    suffix = :suffix,
                    email = :email,
                    year_section = :year_section
                WHERE student_data_id = :student_data_id";
        $query = $this->db->connect()->prepare($sql);

        $query->bindParam(':student_data_id', $this->student_data_id, PDO::PARAM_INT);
        $query->bindParam(':firstname', $this->student_firstname, PDO::PARAM_STR);
        $query->bindParam(':middlename', $this->student_middlename, PDO::PARAM_STR);
        $query->bindParam(':lastname', $this->student_lastname, PDO::PARAM_STR);
        $query->bindParam(':suffix', $this->suffix, PDO::PARAM_STR);
        $query->bindParam(':email', $this->email, PDO::PARAM_STR);
        $query->bindParam(':year_section', $this->year_section, PDO::PARAM_STR);

        return $query->execute();
    }

    public function delete($student_data_id)
    {
        $query = "DELETE FROM students WHERE student_data_id = :student_data_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':student_data_id', $student_data_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>