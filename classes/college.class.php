<?php

require_once 'database.php';

class College
{

    public $college_id;
    public $college_name;
    public $departments;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    // Add a new college
    function add($college_name, $departments)
    {
        $sql = "INSERT INTO colleges_table (college_name, departments) 
            VALUES (:college_name, :departments);";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':college_name', $college_name);
        $query->bindParam(':departments', $departments);

        return $query->execute();
    }

    // Show all colleges
    function show()
    {
        $sql = "SELECT * FROM colleges_table ORDER BY college_id ASC;";

        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    public function showWithDept()
    {
        $sql = "SELECT c.college_id, c.college_name, c.departments, 
                GROUP_CONCAT(d.department_name SEPARATOR ', ') AS department_names
            FROM colleges_table c
            LEFT JOIN college_department_table d 
            ON FIND_IN_SET(d.department_id, c.departments)
            GROUP BY c.college_id
            ORDER BY c.college_id ASC;";

        $query = $this->db->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function showWithCourse()
    {
        $sql = "SELECT 
                c.college_id, 
                c.college_name, 
                c.departments, 
                GROUP_CONCAT(d.department_id, ':', d.department_name SEPARATOR ', ') AS department_data
            FROM 
                colleges_table c
            LEFT JOIN 
                college_department_table d 
            ON 
                FIND_IN_SET(d.department_id, c.departments)
            GROUP BY 
                c.college_id
            ORDER BY 
                c.college_id ASC;";

        $query = $this->db->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function searchByDeptName($keyword)
    {
        $sql = "
        SELECT 
            c.college_id, 
            c.college_name, 
            c.departments, 
           GROUP_CONCAT(d.department_id, ':', d.department_name SEPARATOR ', ') AS department_data
        FROM 
            colleges_table c
        LEFT JOIN 
            college_department_table d 
        ON 
            FIND_IN_SET(d.department_id, c.departments)
        WHERE 
            d.department_name LIKE :keyword
        GROUP BY 
            c.college_id
        ORDER BY 
            c.college_id ASC;
    ";

        $query = $this->db->connect()->prepare($sql);
        $keyword = "%$keyword%";
        $query->bindParam(':keyword', $keyword, PDO::PARAM_STR);

        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    // Show a specific college name by its ID
    public function getCollegeById($college_id)
    {
        $sql = "SELECT * FROM colleges_table WHERE college_id = :college_id";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':college_id', $college_id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function update($college_id, $college_name, $departments)
    {
        $sql = "UPDATE colleges_table SET college_name = :college_name, departments = :departments WHERE college_id = :college_id";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':college_name', $college_name);
        $query->bindParam(':departments', $departments);
        $query->bindParam(':college_id', $college_id, PDO::PARAM_INT);
        return $query->execute();
    }

    // Delete a specific college
    public function delete($college_id)
    {
        $sql = "DELETE FROM colleges_table WHERE college_id = :college_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':college_id', $college_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>