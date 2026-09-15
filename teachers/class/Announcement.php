<?php

class Announcement
{
    public $id;
    public $title;
    public $target_audience;
    public $message;
    public $created_at;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $this->id              = $row['id'];
                    $this->title           = $row['title'];
                    $this->target_audience = $row['target_audience'] ?? 'all';
                    $this->message         = $row['message'] ?? ($row['content'] ?? '');
                    $this->created_at      = $row['created_at'];
                }
            }
            $stmt->close();
        }
    }

    // Fetch All / Teacher Announcements
    public function getTeacherAnnouncements()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

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

    public function all()
    {
        return $this->getTeacherAnnouncements();
    }
}