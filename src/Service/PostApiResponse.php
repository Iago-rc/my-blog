<?php

namespace App\Service;

use App\Dto\PostDto;
use App\Dto\UserDto;

final class PostApiResponse
{

    private array $data;

    public function __construct(string $response)
    {
        $decResponse = json_decode($response, true);
        $this->data = is_array($decResponse) ? $decResponse : [];
    }

    /**
     * Parse post data into an array of PostDto objects.
     *
     * @return PostDto[]|null Array of PostDto objects
     */
    public function parsePosts(): ?array
    {
        $items = $this->normalizeToArray($this->data);

        $results = [];
        foreach ($items as $item) {
            $results[] = new PostDto(
                id: (int) ($item['id'] ?? 0),
                userId: (int) ($item['userId'] ?? 0),
                title: (string) ($item['title'] ?? ''),
                body: (string) ($item['body'] ?? ''),
            );
        }

        return $results;
    }
    /**
     * Parse raw user data into an array of UserDto objects
     *
     * @return UserDto[]|null Array of UserDto objects
     */
    public function parseUsers(): ?array
    {
        $items = $this->normalizeToArray($this->data);

        $results = [];
        foreach ($items as $item) {
            $results[] = new UserDto(
                id: (int) ($item['id'] ?? 0),
                name: (string) ($item['name'] ?? ''),
                username: (string) ($item['username'] ?? ''),
                email: (string) ($item['email'] ?? ''),
            );
        }

        return $results;
    }

    /**
     * Ensure the data is returned as an array of items.
     *
     * @param array $data Input data
     * @return array Normalized array of items.
     */
    private function normalizeToArray(array $data): array
    {
        if (isset($data['id'])) {
            return [$data];
        }

        return $data;
    }
}
