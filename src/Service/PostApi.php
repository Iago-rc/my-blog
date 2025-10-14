<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\PostDto;
use App\Dto\UserDto;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PostApi implements PostApiInterface
{
    private const GET = 'GET';
    private const URL_USERS = 'users';
    private const URL_POSTS = 'posts';
    private const HTTP_CODE_MIN_ERROR = 300;

    private mixed $result = [];
    private string $error = '';
    private readonly string $endpoint;
    private HttpClientInterface $client;
    private PostApiResponse $postApiResponse;

    public function __construct(string $json_placeholder, HttpClientInterface $client) {
        $this->endpoint = $json_placeholder;
        $this->client = $client;
    }

    /**
     * Get posts from the API
     *
     * @param int|null $id
     * @return bool Returns true if the API request was successful, false otherwise
     */
    public function getPosts(?int $id = null): bool
    {
        $url = !empty($id) ? self::URL_POSTS . '/' . $id : self::URL_POSTS;
        $request = $this->callMethod(self::GET, $url);
        if($request){
            $this->result = $this->postApiResponse->parsePosts();
        }
        return $request;
    }

    /**
     * Get users from the API
     *
     * @param int|null $id
     * @return bool Returns true if the API request was successful, false otherwise
     */
    public function getUsers(?int $id = null): bool
    {
        $url = !empty($id) ? self::URL_USERS . '/' . $id : self::URL_USERS;
        $request =  $this->callMethod(self::GET, $url);
        if($request){
            $this->result = $this->postApiResponse->parseUsers();
        }
        return $request;
    }

    /**
     * Make an HTTP request to the API
     *
     * @param string $method HTTP
     * @param string $url Endpoint URL
     * @return bool Returns true if the request succeeded
     */
    private function callMethod(string $method, string $url): bool
    {
        try {
            $response = $this->client->request($method, "{$this->endpoint}/" . $url);
            $content = $response->getContent();
            $this->postApiResponse = new PostApiResponse($content);
            return true;
        } catch (HttpExceptionInterface $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    private function setError(string $error, array $attrs = []): void
    {
        $error = strip_tags($error);
        $this->error = $error;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getResult(): ?array
    {
        return $this->result;
    }

}