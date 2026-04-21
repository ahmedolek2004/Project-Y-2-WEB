<?php
// ============================================================
//  classes/Prescription.php
// ============================================================
class Prescription {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function add(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO prescriptions
             (record_id, medication_name, dosage, instructions)
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([
            (int)$data['record_id'],
            htmlspecialchars(trim($data['medication_name'])),
            htmlspecialchars(trim($data['dosage'])),
            htmlspecialchars(trim($data['instructions'] ?? ''))
        ]);
    }

    public function getByRecord(int $recordId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM prescriptions WHERE record_id = ?
             ORDER BY prescribed_at ASC"
        );
        $stmt->execute([$recordId]);
        return $stmt->fetchAll();
    }
}
