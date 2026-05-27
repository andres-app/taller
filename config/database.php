
<?php
class Database {
    private $host = "localhost";
    private $db_name = "u274409976_taller";
    private $username = "u274409976_taller";
    private $password = "Dev2804751$$$";
    public $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->db_name,
                $this->username,
                $this->password
            );

            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
