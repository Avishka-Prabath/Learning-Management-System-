<?php

class Contact
{
    public $id;
    public $name;
    public $email;
    public $subject;
    public $message;
    public $status;
    public $created_at;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;
            
            $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $this->id         = $row['id'];
                    $this->name       = $row['name'];
                    $this->email      = $row['email'];
                    $this->subject    = $row['subject'];
                    $this->message    = $row['message'];
                    $this->status     = $row['status'] ?? 'Unread';
                    $this->created_at = $row['created_at'];
                }
            }
            $stmt->close();
        }
    }

    // Save contact message
    public function create()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, ?)");
        
        $status = $this->status ?? 'Unread';

        $stmt->bind_param("sssss", 
            $this->name, 
            $this->email, 
            $this->subject, 
            $this->message, 
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

    // Fetch all contact messages
    public function all()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $query = "SELECT * FROM contact_messages ORDER BY id DESC";
        $result = $conn->query($query);

        $array_res = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($array_res, $row);
            }
        }
        return $array_res;
    }

    // Delete contact message
    public function delete()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->bind_param("i", $this->id);

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}