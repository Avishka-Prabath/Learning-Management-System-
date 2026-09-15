<?php

class EditTeacher
{
    public $id;
    public $title;
    public $full_name;
    public $email;
    public $phone;
    public $department;
    public $assigned_module;
    public $username;
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
                    $this->title           = $row['title'] ?? '';
                    $this->full_name       = $row['full_name'] ?? '';
                    $this->email           = $row['email'] ?? '';
                    $this->phone           = $row['phone'] ?? '';
                    $this->department      = $row['department'] ?? '';
                    $this->assigned_module = $row['assigned_module'] ?? '';
                    $this->username        = $row['username'] ?? '';
                    $this->password        = $row['password'] ?? '';
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

    public function usernameExists($username, $exclude_id = 0)
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("SELECT id FROM teachers WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $exclude_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = $res->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function update()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        // 1. Update Teacher Record
        if (!empty($this->password)) {
            $stmt = $conn->prepare("UPDATE teachers SET title = ?, full_name = ?, email = ?, phone = ?, department = ?, assigned_module = ?, username = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssssssssi", 
                $this->title, 
                $this->full_name, 
                $this->email, 
                $this->phone, 
                $this->department, 
                $this->assigned_module, 
                $this->username, 
                $this->password,
                $this->id
            );
        } else {
            $stmt = $conn->prepare("UPDATE teachers SET title = ?, full_name = ?, email = ?, phone = ?, department = ?, assigned_module = ?, username = ? WHERE id = ?");
            $stmt->bind_param("sssssssi", 
                $this->title, 
                $this->full_name, 
                $this->email, 
                $this->phone, 
                $this->department, 
                $this->assigned_module, 
                $this->username,
                $this->id
            );
        }

        $res = $stmt->execute();
        $stmt->close();

        // 2. Link Instructor ID directly to the courses/modules table
        if ($res && !empty($this->assigned_module)) {
            $modId = intval($this->assigned_module);
            $stmtMod = $conn->prepare("UPDATE courses SET teacher_id = ? WHERE id = ?");
            $stmtMod->bind_param("ii", $this->id, $modId);
            $stmtMod->execute();
            $stmtMod->close();
        }

        return $res;
    }
}