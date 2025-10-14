<?php

namespace App\Service;

interface PostApiInterface
{
    public function getPosts(?int $id = null): bool;
    public function getUsers(?int $id = null): bool;
    public function getError(): string;
    public function getResult(): ?array;
}