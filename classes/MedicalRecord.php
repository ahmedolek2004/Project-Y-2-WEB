<?php
// ============================================================
//  classes/MedicalRecord.php
//  Standalone entity class with role-aware CRUD
// ============================================================
class MedicalRecord {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT mr.*, 
                    p.name AS patient_name, p.email AS patient_email,
                    d.name AS doctor_name
             FROM medical_records mr
             JOIN users p ON p.id = mr.patient_id
             JOIN users d ON d.id = mr.doctor_id
             WHERE mr.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getByPatient(int $patientId): array {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS doctor_name
             FROM medical_records mr
             JOIN users u ON u.id = mr.doctor_id
             WHERE mr.patient_id = ?
             ORDER BY mr.visit_date DESC"
        );
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }

    public function getByDoctor(int $doctorId): array {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS patient_name
             FROM medical_records mr
             JOIN users u ON u.id = mr.patient_id
             WHERE mr.doctor_id = ?
             ORDER BY mr.visit_date DESC"
        );
        $stmt->execute([$doctorId]);
        return $stmt->fetchAll();
    }

    public function getPrescriptions(int $recordId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM prescriptions
             WHERE record_id = ?
             ORDER BY prescribed_at ASC"
        );
        $stmt->execute([$recordId]);
        return $stmt->fetchAll();
    }
}
