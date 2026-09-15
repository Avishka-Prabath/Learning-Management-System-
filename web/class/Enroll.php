<?php

class Enroll
{
    public $id;
    public $full_name;
    public $nic;
    public $dob;
    public $gender;
    public $phone;
    public $email;
    public $address;
    public $course_id;
    public $study_mode;
    public $intake;
    public $qualification;
    public $school;
    public $guardian_name;
    public $guardian_phone;
    public $guardian_relation;
    public $notes;
    public $status;
    public $created_at;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;
            
            $stmt = $conn->prepare("SELECT * FROM enrollments WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $this->id                = $row['id'];
                    $this->full_name         = $row['full_name'];
                    $this->nic               = $row['nic'];
                    $this->dob               = $row['dob'];
                    $this->gender            = $row['gender'];
                    $this->phone             = $row['phone'];
                    $this->email             = $row['email'];
                    $this->address           = $row['address'];
                    $this->course_id         = $row['course_id'];
                    $this->study_mode        = $row['study_mode'];
                    $this->intake            = $row['intake'];
                    $this->qualification     = $row['qualification'];
                    $this->school            = $row['school'];
                    $this->guardian_name     = $row['guardian_name'];
                    $this->guardian_phone    = $row['guardian_phone'];
                    $this->guardian_relation = $row['guardian_relation'];
                    $this->notes             = $row['notes'];
                    $this->status            = $row['status'] ?? 'Pending';
                    $this->created_at        = $row['created_at'];
                }
            }
            $stmt->close();
        }
    }

    // Save enrollment
    public function create()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("INSERT INTO enrollments (full_name, nic, dob, gender, phone, email, address, course_id, study_mode, intake, qualification, school, guardian_name, guardian_phone, guardian_relation, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $status = $this->status ?? 'Pending';

        $stmt->bind_param("sssssssisssssssss", 
            $this->full_name, 
            $this->nic, 
            $this->dob, 
            $this->gender, 
            $this->phone, 
            $this->email, 
            $this->address, 
            $this->course_id, 
            $this->study_mode, 
            $this->intake, 
            $this->qualification, 
            $this->school, 
            $this->guardian_name, 
            $this->guardian_phone, 
            $this->guardian_relation, 
            $this->notes, 
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

    // Fetch all enrollments
    public function all()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $query = "SELECT e.*, c.course_name, c.course_code FROM enrollments e LEFT JOIN courses c ON e.course_id = c.id ORDER BY e.id DESC";
        $result = $conn->query($query);

        $array_res = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($array_res, $row);
            }
        }
        return $array_res;
    }

    // Delete enrollment
    public function delete()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("DELETE FROM enrollments WHERE id = ?");
        $stmt->bind_param("i", $this->id);

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}