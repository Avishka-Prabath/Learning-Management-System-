<?php

class Assignment
{
    public $id;
    public $title;
    public $course_name;
    public $deadline_date;
    public $deadline_time;
    public $file_path;
    public $type;
    public $status;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT * FROM assignments WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $this->id            = $row['id'];
                    $this->title         = $row['title'];
                    $this->course_name   = $row['course_name'];
                    $this->deadline_date = $row['deadline_date'];
                    $this->deadline_time = $row['deadline_time'];
                    $this->file_path     = $row['file_path'];
                    $this->type          = $row['type'];
                    $this->status        = $row['status'] ?? 'Active';
                }
            }
            $stmt->close();
        }
    }

    // Save Assignment
    public function create()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("INSERT INTO assignments (title, course_name, deadline_date, deadline_time, file_path, type, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $status = $this->status ?? 'Active';

        $stmt->bind_param("sssssss", $this->title, $this->course_name, $this->deadline_date, $this->deadline_time, $this->file_path, $this->type, $status);

        if ($stmt->execute()) {
            $insert_id = $stmt->insert_id;
            $stmt->close();
            return $insert_id;
        }
        $stmt->close();
        return false;
    }

    // Fetch all assignments
    public function all()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $query = "SELECT * FROM assignments ORDER BY deadline_date DESC";
        $result = $conn->query($query);

        $array_res = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($array_res, $row);
            }
        }
        return $array_res;
    }

    // Update Assignment details
    public function update()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("UPDATE assignments SET title = ?, course_name = ?, deadline_date = ?, deadline_time = ?, type = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $this->title, $this->course_name, $this->deadline_date, $this->deadline_time, $this->type, $this->id);

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Delete Assignment
    public function delete()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("DELETE FROM assignments WHERE id = ?");
        $stmt->bind_param("i", $this->id);

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}