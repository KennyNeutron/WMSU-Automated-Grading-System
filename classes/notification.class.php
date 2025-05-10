<?php

require_once 'database.php';

class Notification
{

    public $notif_id;
    public $user_role;
    public $user_id;
    public $message;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function add()
    {
        $sql = "INSERT INTO notifications (user_role, user_id, message) VALUES (:user_role, :user_id, :message)";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':user_role', $this->user_role, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $this->message, PDO::PARAM_STR);
        return $stmt->execute();
    }

    function show_admin($user_role)
    {
        $sql = "SELECT * FROM notifications WHERE user_role = :user_role ORDER BY created_at ASC;";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':user_role', $user_role, PDO::PARAM_INT);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    function show_user($user_id)
    {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at ASC;";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    public function delete($notif_id)
    {
        $query = "DELETE FROM notifications WHERE notif_id = :notif_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':notif_id', $notif_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteAllAdmin($user_role)
    {
        $query = "DELETE FROM notifications WHERE user_role = :user_role";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':user_role', $user_role, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>