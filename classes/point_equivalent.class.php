<?php
require_once 'database.php';

class PointEqv
{
    public $point_eqv_id;
    public $faculty_sub_id;
    public $rating_1_00;
    public $rating_1_25;
    public $rating_1_50;
    public $rating_1_75;
    public $rating_2_00;
    public $rating_2_25;
    public $rating_2_50;
    public $rating_2_75;
    public $rating_3_00;
    public $rating_5_00;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function add()
    {
        $sql = "INSERT INTO point_equivalent 
                (faculty_sub_id, 1_00, 1_25, 1_50, 1_75, 2_00, 2_25, 2_50, 2_75, 3_00, 5_00) 
                VALUES 
                (:faculty_sub_id, :rating_1_00, :rating_1_25, :rating_1_50, :rating_1_75, 
                 :rating_2_00, :rating_2_25, :rating_2_50, :rating_2_75, :rating_3_00, :rating_5_00)";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $this->faculty_sub_id);
        $query->bindParam(':rating_1_00', $this->rating_1_00);
        $query->bindParam(':rating_1_25', $this->rating_1_25);
        $query->bindParam(':rating_1_50', $this->rating_1_50);
        $query->bindParam(':rating_1_75', $this->rating_1_75);
        $query->bindParam(':rating_2_00', $this->rating_2_00);
        $query->bindParam(':rating_2_25', $this->rating_2_25);
        $query->bindParam(':rating_2_50', $this->rating_2_50);
        $query->bindParam(':rating_2_75', $this->rating_2_75);
        $query->bindParam(':rating_3_00', $this->rating_3_00);
        $query->bindParam(':rating_5_00', $this->rating_5_00);

        return $query->execute();
    }

    function show()
    {
        $sql = "SELECT * FROM point_equivalent";
        $query = $this->db->connect()->prepare($sql);

        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    function getByFacultySubject($faculty_sub_id)
    {
        $sql = "SELECT * FROM point_equivalent 
                WHERE faculty_sub_id = :faculty_sub_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $faculty_sub_id);

        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }

    function getById($point_eqv_id)
    {
        $sql = "SELECT * FROM point_equivalent WHERE point_eqv_id = :point_eqv_id";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':point_eqv_id', $point_eqv_id);

        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }
    public function updateNumericalRating($point_eqv_id, $grade_key, $numerical_rating)
    {
        $sql = "UPDATE point_equivalent SET $grade_key = ? WHERE point_eqv_id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        return $stmt->execute([$numerical_rating, $point_eqv_id]);
    }

    public function update()
    {
        $sql = "UPDATE point_equivalent SET
                1_00 = :rating_1_00,
                1_25 = :rating_1_25,
                1_50 = :rating_1_50,
                1_75 = :rating_1_75,
                2_00 = :rating_2_00,
                2_25 = :rating_2_25,
                2_50 = :rating_2_50,
                2_75 = :rating_2_75,
                3_00 = :rating_3_00,
                5_00 = :rating_5_00
                WHERE point_eqv_id = :point_eqv_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':point_eqv_id', $this->point_eqv_id);
        $query->bindParam(':rating_1_00', $this->rating_1_00);
        $query->bindParam(':rating_1_25', $this->rating_1_25);
        $query->bindParam(':rating_1_50', $this->rating_1_50);
        $query->bindParam(':rating_1_75', $this->rating_1_75);
        $query->bindParam(':rating_2_00', $this->rating_2_00);
        $query->bindParam(':rating_2_25', $this->rating_2_25);
        $query->bindParam(':rating_2_50', $this->rating_2_50);
        $query->bindParam(':rating_2_75', $this->rating_2_75);
        $query->bindParam(':rating_3_00', $this->rating_3_00);
        $query->bindParam(':rating_5_00', $this->rating_5_00);

        return $query->execute();
    }

    public function delete($point_eqv_id)
    {
        $query = "DELETE FROM point_equivalent WHERE point_eqv_id = :point_eqv_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':point_eqv_id', $point_eqv_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getEquivalentForRating($numerical_rating, $faculty_sub_id = null)
    {
        $column_name = str_replace('.', '_', number_format($numerical_rating, 2));

        $sql = "SELECT $column_name FROM point_equivalent";

        if ($faculty_sub_id) {
            $sql .= " WHERE faculty_sub_id = :faculty_sub_id";
        }

        $sql .= " LIMIT 1";

        $query = $this->db->connect()->prepare($sql);

        if ($faculty_sub_id) {
            $query->bindParam(':faculty_sub_id', $faculty_sub_id);
        }

        if ($query->execute()) {
            $result = $query->fetch();
            return $result ? $result[$column_name] : null;
        }
        return null;
    }

    public function getAllEquivalents($faculty_sub_id = null)
    {
        $sql = "SELECT * FROM point_equivalent";

        if ($faculty_sub_id) {
            $sql .= " WHERE faculty_sub_id = :faculty_sub_id";
        }

        $sql .= " LIMIT 1";

        $query = $this->db->connect()->prepare($sql);

        if ($faculty_sub_id) {
            $query->bindParam(':faculty_sub_id', $faculty_sub_id);
        }

        if ($query->execute()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                // Remove non-rating columns
                unset($result['point_eqv_id']);
                unset($result['faculty_sub_id']);

                // Convert column names back to decimal format (1_25 => 1.25)
                $equivalents = [];
                foreach ($result as $key => $value) {
                    $rating = str_replace('_', '.', $key);
                    $equivalents[$rating] = $value;
                }
                return $equivalents;
            }
        }
        return null;
    }
}
?>