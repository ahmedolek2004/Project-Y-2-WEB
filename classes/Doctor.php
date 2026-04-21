<?php
// ============================================================
//  classes/Doctor.php
//  extends User — doctor-only operations
// ============================================================
class Doctor extends User {

    // ── Get my patients (patients with records I created) ────
    public function getMyPatients(): array {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT u.id, u.name, u.email, u.phone,
                    COUNT(mr.id) AS record_count
             FROM users u
             JOIN medical_records mr ON mr.patient_id = u.id
             WHERE mr.doctor_id = ?
             GROUP BY u.id
             ORDER BY u.name"
        );
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll();
    }

    // ── Add medical record ───────────────────────────────────
    public function addRecord(array $data): bool|string {
        if (empty($data['patient_id']) || empty($data['diagnosis']) || empty($data['visit_date'])) {
            return "Patient, diagnosis and visit date are required.";
        }
        // Make sure patient_id belongs to a patient role
        $stmt = $this->db->prepare(
            "SELECT id FROM users WHERE id = ? AND role = 'patient'"
        );
        $stmt->execute([$data['patient_id']]);
        if (!$stmt->fetch()) {
            return "Invalid patient selected.";
        }

        $stmt = $this->db->prepare(
            "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, notes, visit_date)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            (int)$data['patient_id'],
            (int)$_SESSION['user_id'],
            htmlspecialchars(trim($data['diagnosis'])),
            htmlspecialchars(trim($data['notes'] ?? '')),
            $data['visit_date']
        ]);
        return true;
    }

    // ── Update diagnosis (only own records) ──────────────────
    public function updateDiagnosis(int $recordId, array $data): bool|string {
        if (empty($data['diagnosis'])) {
            return "Diagnosis cannot be empty.";
        }
        // Security: only update if this record belongs to this doctor
        $stmt = $this->db->prepare(
            "UPDATE medical_records
             SET diagnosis = ?, notes = ?
             WHERE id = ? AND doctor_id = ?"
        );
        $stmt->execute([
            htmlspecialchars(trim($data['diagnosis'])),
            htmlspecialchars(trim($data['notes'] ?? '')),
            $recordId,
            (int)$_SESSION['user_id']
        ]);
        if ($stmt->rowCount() === 0) {
            return "Record not found or access denied.";
        }
        return true;
    }

    // ── Add prescription ─────────────────────────────────────
    public function addPrescription(array $data): bool|string {
        if (empty($data['record_id']) || empty($data['medication_name']) || empty($data['dosage'])) {
            return "Record, medication and dosage are required.";
        }
        // Verify the record belongs to this doctor
        $stmt = $this->db->prepare(
            "SELECT id FROM medical_records WHERE id = ? AND doctor_id = ?"
        );
        $stmt->execute([$data['record_id'], $_SESSION['user_id']]);
        if (!$stmt->fetch()) {
            return "Record not found or access denied.";
        }

        $stmt = $this->db->prepare(
            "INSERT INTO prescriptions (record_id, medication_name, dosage, instructions)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            (int)$data['record_id'],
            htmlspecialchars(trim($data['medication_name'])),
            htmlspecialchars(trim($data['dosage'])),
            htmlspecialchars(trim($data['instructions'] ?? ''))
        ]);
        return true;
    }

    // ── View my patients' records ────────────────────────────
    public function getMyRecords(): array {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS patient_name, u.email AS patient_email
             FROM medical_records mr
             JOIN users u ON u.id = mr.patient_id
             WHERE mr.doctor_id = ?
             ORDER BY mr.visit_date DESC"
        );
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll();
    }

    // ── Get single record (only own) ─────────────────────────
    public function getRecord(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS patient_name
             FROM medical_records mr
             JOIN users u ON u.id = mr.patient_id
             WHERE mr.id = ? AND mr.doctor_id = ?"
        );
        $stmt->execute([$id, $_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    }

    // ── Search patients ⭐ Bonus ──────────────────────────────
    public function searchPatients(string $keyword): array {
        $like = '%' . $keyword . '%';
        $stmt = $this->db->prepare(
            "SELECT DISTINCT u.id, u.name, u.email, u.phone
             FROM users u
             WHERE u.role = 'patient'
             AND (u.name LIKE ? OR u.email LIKE ?)
             ORDER BY u.name"
        );
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    // ── Dashboard stats ⭐ Bonus ──────────────────────────────
    public function getDashboardStats(): array {
        $doctorId = $_SESSION['user_id'];
        $stats    = [];

        $stmt = $this->db->prepare(
            "SELECT COUNT(DISTINCT patient_id) AS patient_count,
                    COUNT(*) AS record_count
             FROM medical_records WHERE doctor_id = ?"
        );
        $stmt->execute([$doctorId]);
        $stats['overview'] = $stmt->fetch();

        // Recent 5 records
        $stmt = $this->db->prepare(
            "SELECT mr.id, mr.diagnosis, mr.visit_date, u.name AS patient_name
             FROM medical_records mr
             JOIN users u ON u.id = mr.patient_id
             WHERE mr.doctor_id = ?
             ORDER BY mr.visit_date DESC LIMIT 5"
        );
        $stmt->execute([$doctorId]);
        $stats['recent_records'] = $stmt->fetchAll();

        return $stats;
    }

    // ── Get all patients list (for dropdown) ─────────────────
    public function getAllPatients(): array {
        $stmt = $this->db->prepare(
            "SELECT id, name, email FROM users
             WHERE role = 'patient' ORDER BY name"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
