<?php
class Validator {
    private array $errors = [];

    public function required(string $field, mixed $value): self {
        if (empty(trim((string)$value))) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
        return $this;
    }

    public function email(string $field, string $value): self {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Please enter a valid email address.';
        }
        return $this;
    }

    public function minLength(string $field, string $value, int $min): self {
        if (strlen($value) < $min) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.";
        }
        return $this;
    }

    public function inArray(string $field, mixed $value, array $allowed): self {
        if (!in_array($value, $allowed, true)) {
            $this->errors[$field] = 'Invalid value for ' . str_replace('_', ' ', $field) . '.';
        }
        return $this;
    }

    public function date(string $field, string $value): self {
        $d = DateTime::createFromFormat('Y-m-d', $value);
        if (!$d || $d->format('Y-m-d') !== $value) {
            $this->errors[$field] = 'Please enter a valid date (YYYY-MM-DD).';
        }
        return $this;
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(): string {
        return array_values($this->errors)[0] ?? '';
    }

    public static function sanitize(string $value): string {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }
}
