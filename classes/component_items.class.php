<?php

require_once 'database.php';

class ComponentItems
{
    public $items_id;
    public $component_id;
    public $component_date;
    public $component_no;
    public $component_quantity;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function add()
    {
        $sql = "INSERT INTO component_items (component_id, component_date, component_no, component_quantity) VALUES
        (:component_id, :component_date, :component_no, :component_quantity);";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':component_id', $this->component_id);
        $query->bindParam(':component_date', $this->component_date);
        $query->bindParam(':component_no', $this->component_no);
        $query->bindParam(':component_quantity', $this->component_quantity);

        return $query->execute();
    }

    function show()
    {
        $sql = "SELECT * FROM component_items ORDER BY component_id ASC;";

        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    function getItemById($component_id)
    {
        $sql = "SELECT * FROM component_items WHERE component_id = :component_id ORDER BY component_no ASC";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':component_id', $component_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    function getItemId($items_id)
    {
        $sql = "SELECT * FROM component_items WHERE items_id = :items_id ORDER BY component_no ASC";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':items_id', $items_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }
    public function update()
    {
        $sql = "UPDATE component_items SET component_no = :component_no, component_quantity = :component_quantity, component_date = :component_date WHERE items_id = :items_id";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':items_id', $this->items_id);
        $query->bindParam(':component_no', $this->component_no);
        $query->bindParam(':component_quantity', $this->component_quantity);
        $query->bindParam(':component_date', $this->component_date);
        return $query->execute();
    }
    public function delete($items_id)
    {
        $query = "DELETE FROM component_items WHERE items_id = :items_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':items_id', $items_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>
