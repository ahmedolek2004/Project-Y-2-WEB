<?php
// ============================================================
//  classes/Patient.php
//  extends User — patient-only operations
// ============================================================
class Patient extends User {

    // ── Get MY records only ──────────────────────────────────
    public function getMyRecords(): array {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS doctor_name
             FROM medical_records mr
             JOIN users u ON u.id = mr.doctor_id
             WHERE mr.patient_id = ?
             ORDER BY mr.visit_date DESC"
        );
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll();
    }

    // ── Get MY prescriptions only ────────────────────────────
    public function getMyPrescriptions(): array {
        $stmt = $this->db->prepare(
            "SELECT p.*, mr.diagnosis, mr.visit_date,
                    u.name AS doctor_name
             FROM prescriptions p
             JOIN medical_records mr ON mr.id = p.record_id
             JOIN users u ON u.id = mr.doctor_id
             WHERE mr.patient_id = ?
             ORDER BY p.prescribed_at DESC"
        );
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll();
    }

    // ── Timeline ⭐ Bonus — sorted ASC for progression ───────
    public function getTimeline(): array {
        $stmt = $this->db->prepare(
            "SELECT mr.id, mr.diagnosis, mr.notes,
                    mr.visit_date, mr.created_at,
                    u.name AS doctor_name,
                    COUNT(p.id) AS prescription_count
             FROM medical_records mr
             JOIN users u ON u.id = mr.doctor_id
             LEFT JOIN prescriptions p ON p.record_id = mr.id
             WHERE mr.patient_id = ?
             GROUP BY mr.id
             ORDER BY mr.visit_date ASC"
        );
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll();
    }

    // ── Get single record (security: only own) ───────────────
    public function getRecord(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT mr.*, u.name AS doctor_name
             FROM medical_records mr
             JOIN users u ON u.id = mr.doctor_id
             WHERE mr.id = ? AND mr.patient_id = ?"
        );
        $stmt->execute([$id, $_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    }

    // ── Dashboard summary ────────────────────────────────────
    public function getDashboardStats(): array {
        $patientId = $_SESSION['user_id'];
        $stats     = [];

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS record_count FROM medical_records
             WHERE patient_id = ?"
        );
        $stmt->execute([$patientId]);
        $stats['record_count'] = $stmt->fetch()['record_count'];

        $stmt = $this->db->prepare(
            "SELECT COUNT(p.id) AS presc_count
             FROM prescriptions p
             JOIN medical_records mr ON mr.id = p.record_id
             WHERE mr.patient_id = ?"
        );
        $stmt->execute([$patientId]);
        $stats['presc_count'] = $stmt->fetch()['presc_count'];

        // Last visit
        $stmt = $this->db->prepare(
            "SELECT visit_date, diagnosis, u.name AS doctor_name
             FROM medical_records mr
             JOIN users u ON u.id = mr.doctor_id
             WHERE mr.patient_id = ?
             ORDER BY visit_date DESC LIMIT 1"
        );
        $stmt->execute([$patientId]);
        $stats['last_visit'] = $stmt->fetch();

        return $stats;
    }
}
