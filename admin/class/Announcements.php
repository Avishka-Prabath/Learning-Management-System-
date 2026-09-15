<?php

class Announcements
{
    public $id;
    public $target_audience;
    public $title;
    public $message;
    public $created_at;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

            $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $this->id              = $row['id'];
                    $this->target_audience = $row['target_audience'];
                    $this->title           = $row['title'];
                    $this->message         = $row['message'];
                    $this->created_at      = $row['created_at'];
                }
            }
            $stmt->close();
        }
    }

    public function create()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("INSERT INTO announcements (target_audience, title, message) VALUES (?, ?, ?)");

        $stmt->bind_param("sss", 
            $this->target_audience, 
            $this->title, 
            $this->message
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

        $query = "SELECT * FROM announcements ORDER BY id DESC";
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

        $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $this->id);

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}