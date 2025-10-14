<?php

namespace App\Tests\Service;

use App\Service\PostApiInterface;

class PostApiStub implements PostApiInterface
{
    private ?array $result;
    private string $error;

    private array $posts = [
        ['id' => 1, 'title' => 'Primer post', 'body' => 'Body de prueba'],
        ['id' => 2, 'title' => 'Segundo post', 'body' => 'Otro body contenido de prueba'],
    ];

    private array $users = [
        ['id' => 1, 'name' => 'Usuario 1', 'username' => 'Username 1', 'email' => 'user1@example.com'],
        ['id' => 2, 'name' => 'Usuario 2', 'username' => 'Username 2', 'email' => 'user2@example.com'],
    ];


    public function getPosts(?int $id = null): bool
    {
        if ($id === null) {
            $this->result = $this->posts;
        } else {
            $this->result = array_filter($this->posts, fn($post) => $post['id'] === $id);
            if (empty($this->result)) {

                $this->error = "Post con id $id no encontrado";
                $this->result = null;
                return false;
            }
        }
        $this->error = '';
        return true;
    }

    public function getUsers(?int $id = null): bool
    {
        if ($id === null) {
            $this->result = $this->users;
        } else {
            $this->result = array_filter($this->users, fn($user) => $user['id'] === $id);

            if (empty($this->result)) {
                $this->error = "Usuario con id $id no encontrado";
                $this->result = null;
                return false;
            }
        }
        $this->error = '';
        
        return true;
    }

    public function getResult(): ?array
    {
        return $this->result;
    }

    public function getError(): string
    {
        return $this->error;
    }
}