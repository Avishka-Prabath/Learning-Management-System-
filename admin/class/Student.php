<?php

class Student
{
    public $id;
    public $enrollment_id;
    public $student_id;
    public $campus_id;
    public $nic;
    public $first_name;
    public $last_name;
    public $full_name;
    public $dob;
    public $gender;
    public $email;
    public $campus_email;
    public $phone;
    public $address;
    public $emergency_contact;
    public $course_id;
    public $study_mode;
    public $intake;
    public $qualification;
    public $school;
    public $guardian_name;
    public $guardian_phone;
    public $guardian_relation;
    public $profile_photo;
    public $password;
    public $status;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

            $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $this->id                = $row['id'];
                    $this->enrollment_id     = $row['enrollment_id'] ?? NULL;
                    $this->student_id        = $row['student_id'] ?? $row['campus_id'] ?? NULL;
                    $this->campus_id         = $row['campus_id'] ?? NULL;
                    $this->nic               = $row['nic'] ?? NULL;
                    $this->first_name        = $row['first_name'] ?? NULL;
                    $this->last_name         = $row['last_name'] ?? NULL;
                    $this->full_name         = $row['full_name'] ?? NULL;
                    $this->dob               = $row['dob'] ?? NULL;
                    $this->gender            = $row['gender'] ?? NULL;
                    $this->email             = $row['email'] ?? NULL;
                    $this->campus_email      = $row['campus_email'] ?? NULL;
                    $this->phone             = $row['phone'] ?? NULL;
                    $this->address           = $row['address'] ?? NULL;
                    $this->emergency_contact = $row['guardian_phone'] ?? NULL;
                    $this->course_id         = $row['course_id'] ?? NULL;
                    $this->study_mode        = $row['study_mode'] ?? NULL;
                    $this->intake            = $row['intake'] ?? NULL;
                    $this->qualification     = $row['qualification'] ?? NULL;
                    $this->school            = $row['school'] ?? NULL;
                    $this->guardian_name     = $row['guardian_name'] ?? NULL;
                    $this->guardian_phone    = $row['guardian_phone'] ?? NULL;
                    $this->guardian_relation = $row['guardian_relation'] ?? NULL;
                    $this->profile_photo     = $row['profile_photo'] ?? NULL;
                    $this->password          = $row['password'] ?? NULL;
                    $this->status            = $row['status'] ?? 'Active';
                }
            }
            $stmt->close();
        }
    }

    public function emailExists($email, $exclude_id = 0)
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("SELECT id FROM students WHERE (email = ? OR campus_email = ?) AND id != ?");
        $stmt->bind_param("ssi", $email, $email, $exclude_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = $res->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function campusEmailExists($campus_email, $exclude_id = 0)
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $stmt = $conn->prepare("SELECT id FROM students WHERE campus_email = ? AND id != ?");
        $stmt->bind_param("si", $campus_email, $exclude_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = $res->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function create()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $student_id = !empty($this->student_id) ? $this->student_id : (!empty($this->campus_id) ? $this->campus_id : 'EM-2026-' . rand(1000, 9999));
        $status = $this->status ?? 'Active';
        $enrollment_id = !empty($this->enrollment_id) ? intval($this->enrollment_id) : NULL;
        $course_id = !empty($this->course_id) ? intval($this->course_id) : 0;

        $stmt = $conn->prepare("INSERT INTO students (enrollment_id, student_id, campus_id, nic, first_name, last_name, full_name, dob, gender, email, campus_email, phone, address, course_id, study_mode, intake, qualification, school, guardian_name, guardian_phone, guardian_relation, profile_photo, password, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Exact 24 parameters binding match
        $stmt->bind_param("issssssssssssissssssssss", 
            $enrollment_id, 
            $student_id,
            $this->campus_id, 
            $this->nic, 
            $this->first_name, 
            $this->last_name, 
            $this->full_name, 
            $this->dob, 
            $this->gender, 
            $this->email, 
            $this->campus_email, 
            $this->phone, 
            $this->address, 
            $course_id, 
            $this->study_mode, 
            $this->intake, 
            $this->qualification, 
            $this->school, 
            $this->guardian_name, 
            $this->guardian_phone, 
            $this->guardian_relation, 
            $this->profile_photo, 
            $this->password, 
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

    public function update()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $student_id = !empty($this->student_id) ? $this->student_id : $this->campus_id;
        $status = $this->status ?? 'Active';
        $course_id = !empty($this->course_id) ? intval($this->course_id) : 0;
        $id = intval($this->id);

        $stmt = $conn->prepare("UPDATE students SET 
            student_id = ?, 
            campus_id = ?, 
            campus_email = ?, 
            password = ?, 
            full_name = ?, 
            first_name = ?, 
            last_name = ?, 
            nic = ?, 
            dob = ?, 
            gender = ?, 
            phone = ?, 
            email = ?, 
            address = ?, 
            course_id = ?, 
            study_mode = ?, 
            intake = ?, 
            qualification = ?, 
            school = ?, 
            guardian_name = ?, 
            guardian_phone = ?, 
            guardian_relation = ?, 
            status = ?
            WHERE id = ?");

        $stmt->bind_param("sssssssssssssissssssssi",
            $student_id,
            $this->campus_id,
            $this->campus_email,
            $this->password,
            $this->full_name,
            $this->first_name,
            $this->last_name,
            $this->nic,
            $this->dob,
            $this->gender,
            $this->phone,
            $this->email,
            $this->address,
            $course_id,
            $this->study_mode,
            $this->intake,
            $this->qualification,
            $this->school,
            $this->guardian_name,
            $this->guardian_phone,
            $this->guardian_relation,
            $status,
            $id
        );

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function all()
    {
        $db = Database::getInstance();
        $conn = method_exists($db, 'getConnection') ? $db->getConnection() : $db->DB_CON;

        $query = "SELECT * FROM students ORDER BY id DESC";
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

        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $this->id);

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
}