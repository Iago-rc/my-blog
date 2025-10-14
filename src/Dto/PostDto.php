<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class PostDto
{

    public int $id;

    #[Assert\NotBlank(message: 'userId is required')]
    #[Assert\Type(type: 'integer', message: 'userId must be an integer')]
    public ?int $userId;

    #[Assert\NotBlank(message: 'title is required')]
    public ?string $title;

    #[Assert\NotBlank(message: 'body is required')]
    public ?string $body;

    #[Assert\NotBlank(message: 'user is required')]
    public UserDto $user;
    
    public function __construct(int $id, ?int $userId, ?string $title, ?string $body)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->body = $body;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setUser(UserDto $user): UserDto
    {
        $this->user = $user;
        return $this->user;
    }

    public function getUser(): UserDto
    {
        return $this->user;
    }
}