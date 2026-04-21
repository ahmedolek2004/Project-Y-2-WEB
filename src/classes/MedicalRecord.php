<?php
require_once __DIR__ . '/Model.php';

class MedicalRecord extends Model {

    public function createRecord(int $patientId, int $doctorId, string $diagnosis, string $notes, string $visitDate): int|false {
        $stmt = $this->db->prepare(
            "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, notes, visit_date)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$patientId, $doctorId, $diagnosis, $notes, $visitDate]);
        return (int)$this->db->lastInsertId();
    }

    public function updateDiagnosis(int $recordId, int $doctorId, string $diagnosis, string $notes): bool {
        // Ensure doctor can only update their OWN records
        $stmt = $this->db->prepare(
            "UPDATE medical_records SET diagnosis = ?, notes = ?
             WHERE id = ? AND doctor_id = ?"
        );
        return $stmt->execute([$diagnosis, $notes, $recordId, $doctorId]);
    }

    public function getRecordsByDoctor(int $doctorId): array {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS patient_name, u.email AS patient_email
             FROM medical_records mr
             JOIN users u ON u.id = mr.patient_id
             WHERE mr.doctor_id = ?
             ORDER BY mr.visit_date DESC"
        );
        $stmt->execute([$doctorId]);
        return $stmt->fetchAll();
    }

    public function getRecordsByPatient(int $patientId): array {
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

    public function getRecordById(int $recordId): array|false {
        $stmt = $this->db->prepare(
            "SELECT mr.*, 
                    p.name AS patient_name, p.email AS patient_email,
                    d.name AS doctor_name
             FROM medical_records mr
             JOIN users p ON p.id = mr.patient_id
             JOIN users d ON d.id = mr.doctor_id
             WHERE mr.id = ?"
        );
        $stmt->execute([$recordId]);
        return $stmt->fetch();
    }

    public function getRecordForDoctor(int $recordId, int $doctorId): array|false {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS patient_name
             FROM medical_records mr
             JOIN users u ON u.id = mr.patient_id
             WHERE mr.id = ? AND mr.doctor_id = ?"
        );
        $stmt->execute([$recordId, $doctorId]);
        return $stmt->fetch();
    }

    public function getRecordForPatient(int $recordId, int $patientId): array|false {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS doctor_name
             FROM medical_records mr
             JOIN users u ON u.id = mr.doctor_id
             WHERE mr.id = ? AND mr.patient_id = ?"
        );
        $stmt->execute([$recordId, $patientId]);
        return $stmt->fetch();
    }

    public function getPatientTimeline(int $patientId): array {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS doctor_name,
                    (SELECT COUNT(*) FROM prescriptions p WHERE p.record_id = mr.id) AS prescription_count
             FROM medical_records mr
             JOIN users u ON u.id = mr.doctor_id
             WHERE mr.patient_id = ?
             ORDER BY mr.visit_date ASC"
        );
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }

    public function getDoctorRecentRecords(int $doctorId, int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS patient_name
             FROM medical_records mr
             JOIN users u ON u.id = mr.patient_id
             WHERE mr.doctor_id = ?
             ORDER BY mr.visit_date DESC
             LIMIT ?"
        );
        $stmt->execute([$doctorId, $limit]);
        return $stmt->fetchAll();
    }

    public function countDoctorPatients(int $doctorId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(DISTINCT patient_id) FROM medical_records WHERE doctor_id = ?"
        );
        $stmt->execute([$doctorId]);
        return (int)$stmt->fetchColumn();
    }

    public function countAll(): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM medical_records");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function deleteRecord(int $recordId, int $doctorId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM medical_records WHERE id = ? AND doctor_id = ?"
        );
        return $stmt->execute([$recordId, $doctorId]);
    }
}
