<?php

class Instructor
{
    public $id;
    public $title;
    public $full_name;
    public $email;
    public $username;
    public $phone;
    public $department;
    public $assigned_module;
    public $profile_photo;
    public $password;
    public $status;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

            $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $this->id              = $row['id'];
                    $this->title           = $row['title'];
                    $this->full_name       = $row['full_name'];
                    $this->email           = $row['email'];
                    $this->username        = $row['username'];
                    $this->phone           = $row['phone'];
                    $this->department      = $row['department'];
                    $this->assigned_module = $row['assigned_module'];
                    $this->profile_photo   = $row['profile_photo'];
                    $this->status          = $row['status'] ?? 'Active';
                }
            }
            $stmt->close();
        }
    }

    public function emailExists($email, $exclude_id = 0)
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("SELECT id FROM teachers WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $exclude_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = $res->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function create()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        //$conn->query("ALTER TABLE teachers ADD COLUMN IF NOT EXISTS username VARCHAR(50) UNIQUE NULL AFTER email");

        $stmt = $conn->prepare("INSERT INTO teachers (title, full_name, email, username, phone, department, assigned_module, profile_photo, password, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $status = $this->status ?? 'Active';

        $stmt->bind_param("ssssssssss", 
            $this->title, 
            $this->full_name, 
            $this->email, 
            $this->username, 
            $this->phone, 
            $this->department, 
            $this->assigned_module, 
            $this->profile_photo, 
            $this->password, 
            $status
        );

        if ($stmt->execute()) {
            $insert_id = $stmt->insert_id;
            $stmt->close();
            return $insert_id;
        }
        $stmt->close();
        return false;
    }

    public function all()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $query = "SELECT * FROM teachers ORDER BY id DESC";
        $result = $conn->query($query);

        $array_res = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($array_res, $row);
            }
        }
        return $array_res;
    }

    public function delete()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt->bind_param("i", $this->id);

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}