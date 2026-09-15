<?php

class Calendar
{
    public $id;
    public $title;
    public $type;
    public $event_date;
    public $start_time;
    public $end_time;
    public $instructor_id;
    public $instructor_info;
    public $created_at;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

            $stmt = $conn->prepare("SELECT * FROM schedules WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $this->id              = $row['id'];
                    $this->title           = $row['title'];
                    $this->type            = $row['type'] ?? 'Live Class';
                    $this->event_date      = $row['event_date'];
                    $this->start_time      = $row['start_time'];
                    $this->end_time        = $row['end_time'];
                    $this->instructor_id   = $row['instructor_id'] ?? 0;
                    $this->created_at      = $row['created_at'] ?? NULL;
                }
            }
            $stmt->close();
        }
    }

    public function create()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("INSERT INTO schedules (title, type, event_date, start_time, end_time, instructor_id) VALUES (?, ?, ?, ?, ?, ?)");

        $inst_id = intval($this->instructor_id);

        // sssssi -> String 5ක් සහ Integer (instructor_id) එකක්
        $stmt->bind_param("sssssi", 
            $this->title, 
            $this->type, 
            $this->event_date, 
            $this->start_time, 
            $this->end_time, 
            $inst_id
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

        // Teachers/Instructors ලාගේ නම්ද එකතු කරගෙන Query කිරීම
        $query = "SELECT s.*, CONCAT(IFNULL(t.title,''), ' ', IFNULL(t.full_name,'')) AS instructor_name 
                  FROM schedules s 
                  LEFT JOIN teachers t ON s.instructor_id = t.id 
                  ORDER BY s.event_date ASC, s.start_time ASC";
        $result = $conn->query($query);

        $array_res = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($array_res, $row);
            }
        }
        return $array_res;
    }

    public function delete() {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
        $stmt->bind_param("i", $this->id);

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}