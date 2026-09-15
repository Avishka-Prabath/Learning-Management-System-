<?php

class Course
{
    public $id;
    public $course_name;
    public $course_code;
    public $description;
    public $status;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $this->id          = $row['id'];
                $this->course_name = $row['course_name'];
                $this->course_code = $row['course_code'] ?? '';
                $this->description = $row['description'] ?? '';
                $this->status      = $row['status'] ?? 'Active';
            }
        }
    }

    // Save Course
    public function create()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("INSERT INTO courses (course_name, course_code, description, status) VALUES (?, ?, ?, ?)");
        
        $status = $this->status ?? 'Active';

        $stmt->bind_param("ssss", $this->course_name, $this->course_code, $this->description, $status);

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        return false;
    }

    // Fetch all courses
    public function all()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $query = "SELECT * FROM courses ORDER BY id DESC";
        $result = $conn->query($query);

        $array_res = array();
        while ($row = $result->fetch_assoc()) {
            array_push($array_res, $row);
        }
        return $array_res;
    }

    // Update Course details
    public function update()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_code = ?, description = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $this->course_name, $this->course_code, $this->description, $this->status, $this->id);

        return $stmt->execute();
    }

    // Delete Course
    public function delete()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $this->id);

        return $stmt->execute();
    }
}