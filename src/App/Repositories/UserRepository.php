<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class UserRepository
{
    public function __construct(private Database $database) {}

    public function getAll(): array
    {
        $pdo = $this->database->getConnection();
        $stmt = $pdo->query("SELECT * FROM users");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(string $column, $value): array|bool
    {
        $sql = "SELECT * FROM users WHERE $column = :value";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO users (name, email, password_hash, api_key, api_key_hash)
                VALUES (:name, :email, :password_hash, :api_key, :api_key_hash)";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':password_hash', $data['password_hash']);
        $stmt->bindValue(':api_key', $data['api_key']);
        $stmt->bindValue(':api_key_hash', $data['api_key_hash']);

        $stmt->execute();
        return $pdo->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $sql = "UPDATE users SET name = :name, email = :email,
                password_hash = :password_hash WHERE id = :id";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':password_hash', $data['password_hash']);
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(int $id): int
    {
        $sql = "DELETE FROM users WHERE id = :id";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
