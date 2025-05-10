<?php

require_once 'database.php';

class Periods
{
    public $period_id;
    public $faculty_sub_id;
    public $period_type;
    public $weight;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function add()
    {
        $sql = "INSERT INTO periods (faculty_sub_id, period_type, weight) VALUES
        (:faculty_sub_id, :period_type, :weight);";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $this->faculty_sub_id);
        $query->bindParam(':period_type', $this->period_type);
        $query->bindParam(':weight', $this->weight);

        return $query->execute();
    }

    function show()
    {
        $sql = "SELECT * FROM periods";

        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    function showDistinct()
    {
        $sql = "SELECT DISTINCT * FROM periods";

        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    function showPeriodBySub($faculty_sub_id)
    {
        $sql = "SELECT * FROM periods WHERE faculty_sub_id = :faculty_sub_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $faculty_sub_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    function getIdMidterm($faculty_sub_id)
    {
        $sql = "SELECT period_id FROM periods WHERE faculty_sub_id = :faculty_sub_id AND period_type = 'Midterm'";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $faculty_sub_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data['period_id'] ?? null;
    }
    function getIdFinalterm($faculty_sub_id)
    {
        $sql = "SELECT period_id FROM periods WHERE faculty_sub_id = :faculty_sub_id AND period_type = 'Final Term'";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $faculty_sub_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data['period_id'] ?? null;
    }
    function showMidterm($faculty_sub_id)
    {
        $sql = "SELECT p.*, c.component_id, c.component_type, c.weight
         FROM periods p
         INNER JOIN sub_components c ON p.period_id = c.period_id
         WHERE period_type = 'Midterm' AND faculty_sub_id = :faculty_sub_id
         ";
//  AND c.component_type != 'attendance'
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $faculty_sub_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    function showMidtermTwo($faculty_sub_id)
    {
        $sql = "SELECT p.*, c.component_id, c.component_type, c.weight
         FROM periods p
         INNER JOIN sub_components c ON p.period_id = c.period_id
         WHERE period_type = 'Midterm' AND faculty_sub_id = :faculty_sub_id
          AND c.component_type != 'attendance'";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $faculty_sub_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
public function getAttendanceMidtermm($faculty_sub_id) {
    $sql = "SELECT p.*, c.component_id, c.component_type, c.weight
        FROM periods p
        INNER JOIN sub_components c ON p.period_id = c.period_id
        WHERE period_type = 'Midterm' 
          AND faculty_sub_id = :faculty_sub_id
          AND c.component_type = 'attendance'";

    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':faculty_sub_id', $faculty_sub_id);
  
    $data = [];

    if ($query->execute()) {
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
    }

    return $data;
}


    function showFinalterm($faculty_sub_id)
    {
        $sql = "SELECT p.*, c.component_id, c.component_type, c.weight
         FROM periods p
         INNER JOIN sub_components c ON p.period_id = c.period_id
         WHERE period_type = 'Final Term' AND faculty_sub_id = :faculty_sub_id
        ";
//  AND c.component_type != 'attendance'
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $faculty_sub_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    function showFinaltermTwo($faculty_sub_id)
    {
        $sql = "SELECT p.*, c.component_id, c.component_type, c.weight
         FROM periods p
         INNER JOIN sub_components c ON p.period_id = c.period_id
         WHERE period_type = 'Final Term' AND faculty_sub_id = :faculty_sub_id
         AND c.component_type != 'attendance'";
// 
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $faculty_sub_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    public function getAttendanceFinalTerm($faculty_sub_id) {
        $sql = "SELECT p.*, c.component_id, c.component_type, c.weight
            FROM periods p
            INNER JOIN sub_components c ON p.period_id = c.period_id
            WHERE period_type = 'Final Term' 
              AND faculty_sub_id = :faculty_sub_id
              AND c.component_type = 'attendance'";
    
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':faculty_sub_id', $faculty_sub_id);
      
        $data = [];
    
        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }
    
        return $data;
    }
    function getPeriodById($period_id)
    {
        $sql = "SELECT * FROM periods WHERE period_id = :period_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':period_id', $period_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }
    public function delete($period_id)
    {
        $query = "DELETE FROM periods WHERE period_id = :period_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':period_id', $period_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>