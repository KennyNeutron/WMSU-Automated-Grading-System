<?php

require_once 'database.php';

class SubjectComponents
{
    public $component_id;
    public $period_id;
    public $component_type;
    public $weight;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function add()
    {
        $sql = "INSERT INTO sub_components (period_id, component_type, weight) VALUES
        (:period_id, :component_type, :weight);";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':period_id', $this->period_id);
        $query->bindParam(':component_type', $this->component_type);
        $query->bindParam(':weight', $this->weight);

        return $query->execute();
    }
    function edit()
    {
        $sql = "UPDATE sub_components SET 
                              component_type=:component_type, 
                              weight=:weight
              WHERE component_id = :component_id;";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':component_type', $this->component_type);
        $query->bindParam(':weight', $this->weight);
        $query->bindParam(':component_id', $this->component_id);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function show()
    {
        $sql = "SELECT * FROM sub_components ORDER BY period_id ASC;";

        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    function getComponentById($component_id)
    {
        $sql = "SELECT * FROM sub_components WHERE component_id = :component_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':component_id', $component_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }

    public function delete($component_id)
    {
        $query = "DELETE FROM sub_components WHERE component_id = :component_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':component_id', $component_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>
