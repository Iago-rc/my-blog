<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class UserDto
{
    public int $id;

    #[Assert\NotBlank(message: 'Name is required')]
    public ?string $name;

    #[Assert\NotBlank(message: 'Username is required')]
    public ?string $username;

    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Email is not valid')]
    public ?string $email;

    public function __construct(int $id, ?string $name, ?string $username, ?string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}