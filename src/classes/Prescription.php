<?php
require_once __DIR__ . '/Model.php';

class Prescription extends Model {

    public function addPrescription(int $recordId, string $medicationName, string $dosage, string $instructions): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO prescriptions (record_id, medication_name, dosage, instructions)
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$recordId, $medicationName, $dosage, $instructions]);
    }

    public function getPrescriptionsByRecord(int $recordId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM prescriptions WHERE record_id = ? ORDER BY created_at ASC"
        );
        $stmt->execute([$recordId]);
        return $stmt->fetchAll();
    }

    public function getPrescriptionsByPatient(int $patientId): array {
        // Joined query that strictly filters by patient ownership
        $stmt = $this->db->prepare(
            "SELECT p.*, mr.visit_date, mr.diagnosis, u.name AS doctor_name
             FROM prescriptions p
             JOIN medical_records mr ON mr.id = p.record_id
             JOIN users u ON u.id = mr.doctor_id
             WHERE mr.patient_id = ?
             ORDER BY mr.visit_date DESC, p.created_at ASC"
        );
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }

    public function deletePrescription(int $prescriptionId, int $doctorId): bool {
        // Verify ownership through join before deleting
        $stmt = $this->db->prepare(
            "DELETE p FROM prescriptions p
             JOIN medical_records mr ON mr.id = p.record_id
             WHERE p.id = ? AND mr.doctor_id = ?"
        );
        return $stmt->execute([$prescriptionId, $doctorId]);
    }

    public function countAll(): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM prescriptions");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
