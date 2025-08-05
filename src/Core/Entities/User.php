<?php

namespace App\Core\Entities;

class User
{
    private ?string $id;
    private string $name;
    private string $email;
    private string $password;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        string $name,
        string $email,
        string $password,
        ?string $id = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        $this->validateName($name);
        $this->validateEmail($email);
        $this->validatePassword($password);

        $this->id = $id ?? uniqid();
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt ?? new \DateTime();
        $this->updatedAt = $updatedAt ?? new \DateTime();
    }

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Nome não pode estar vazio');
        }

        if (strlen($name) < 2) {
            throw new \InvalidArgumentException('Nome deve ter pelo menos 2 caracteres');
        }

        if (strlen($name) > 100) {
            throw new \InvalidArgumentException('Nome não pode ter mais de 100 caracteres');
        }
    }

    private function validateEmail(string $email): void
    {
        if (empty(trim($email))) {
            throw new \InvalidArgumentException('Email não pode estar vazio');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email inválido');
        }
    }

    private function validatePassword(string $password): void
    {
        if (empty($password)) {
            throw new \InvalidArgumentException('Senha não pode estar vazia');
        }

        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('Senha deve ter pelo menos 6 caracteres');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function updateName(string $name): void
    {
        $this->validateName($name);
        $this->name = $name;
        $this->updatedAt = new \DateTime();
    }

    public function updateEmail(string $email): void
    {
        $this->validateEmail($email);
        $this->email = $email;
        $this->updatedAt = new \DateTime();
    }

    public function updatePassword(string $password): void
    {
        $this->validatePassword($password);
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->updatedAt = new \DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }
}
