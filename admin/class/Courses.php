<?php

class Course
{
    public $id;
    public $course_code;
    public $course_name;
    public $category;
    public $teacher_id;
    public $duration;
    public $price;
    public $image;
    public $description;
    public $created_at;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

            $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $this->id          = $row['id'];
                    $this->course_code = $row['course_code'];
                    $this->course_name = $row['course_name'];
                    $this->category    = $row['category'];
                    $this->teacher_id  = $row['teacher_id'];
                    $this->duration    = $row['duration'];
                    $this->price       = $row['price'];
                    $this->image       = $row['image'];
                    $this->description = $row['description'];
                    $this->created_at  = $row['created_at'];
                }
            }
            $stmt->close();
        }
    }

    public function create()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, category, teacher_id, duration, price, image, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssissss", 
            $this->course_code, 
            $this->course_name, 
            $this->category, 
            $this->teacher_id, 
            $this->duration, 
            $this->price, 
            $this->image, 
            $this->description
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

        $query = "SELECT c.*, t.title AS teacher_title, t.full_name AS teacher_name FROM courses c LEFT JOIN teachers t ON c.teacher_id = t.id ORDER BY c.id DESC";
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

        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $this->id);

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}