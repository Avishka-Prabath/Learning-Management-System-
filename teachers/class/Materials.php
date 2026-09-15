<?php

class Materials
{
    public $id;
    public $course_id;
    public $title;
    public $publish_date;
    public $file_path;
    public $external_link;
    public $status;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            $stmt = $conn->prepare("SELECT * FROM course_materials WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $this->id = $row['id'];
                $this->course_id = $row['course_id'];
                $this->title = $row['title'];
                $this->publish_date = $row['publish_date'];
                $this->file_path = $row['file_path'];
                $this->external_link = $row['external_link'];
                $this->status = $row['status'];
            }
        }
    }

    // Save course material
    public function create()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("INSERT INTO course_materials (course_id, title, publish_date, file_path, external_link, status) VALUES (?, ?, ?, ?, ?, ?)");
        
        $status = $this->status ?? 'Active';

        $stmt->bind_param("isssss", $this->course_id, $this->title, $this->publish_date, $this->file_path, $this->external_link, $status);

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        return false;
    }

    // Fetch all materials
    public function all()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $query = "SELECT * FROM course_materials ORDER BY id DESC";
        $result = $conn->query($query);

        $array_res = array();
        while ($row = $result->fetch_assoc()) {
            array_push($array_res, $row);
        }
        return $array_res;
    }
}