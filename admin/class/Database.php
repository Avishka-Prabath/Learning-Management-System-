<?php
if (!class_exists('Database')) {
class Database
{
    private static $instance = null;

    private $host;

    private $name;

    private $user;

    private $password;

    public $DB_CON;

    private function __construct()
    {
        if ($this->isLocalServer()) {

            $this->host = '127.0.0.1';
            $this->name = 'edumart_db';
            $this->user = 'root';
            $this->password = '';
        } 

        $this->DB_CON = mysqli_connect($this->host, $this->user, $this->password, $this->name);

        if (!$this->DB_CON) {
            die("Database connection failed: " . mysqli_connect_error());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function isLocalServer()
    {
        if (!isset($_SERVER['SERVER_NAME'])) {
            return true;
        }
        return in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
    }

    public function readQuery($query)
    {
        $result = mysqli_query($this->DB_CON, $query);

        if (!$result) {
            die("SQL Error: " . mysqli_error($this->DB_CON) . "<br>Query: " . $query);
        }

        return $result;
    }

    public function escapeString($string)
    {
        return mysqli_real_escape_string($this->DB_CON, $string);
    }

    public function getConnection()
    {
        return $this->DB_CON;
    }

    public function __destruct()
    {
        if ($this->DB_CON) {
            mysqli_close($this->DB_CON);
        }
    }
}
}
?>