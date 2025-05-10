<?php 

class Database {
  private $host = 'localhost';
  private $username = 'u701207055_auto_gsdb2';
  private $password = 'bscs3B2023-24';
  private $database = 'u701207055_auto_gsdb2';
  protected $connection;

  function connect() {
    try {
      $this->connection = new PDO("mysql:host=$this->host;dbname=$this->database", $this->username, $this->password);
    }

    catch(PDOException $e) {
      echo "Connection Error!" . $e->getMessage();
    }

    return $this->connection;
  }  
}

?>