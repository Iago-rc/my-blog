<?php

namespace App\Controller;

use App\Dto\PostDto;
use App\Dto\UserDto;
use App\Service\PostApiInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostRestController extends AbstractController
{

    public function __construct(private PostApiInterface $postApi,
    private ValidatorInterface $validator) {
    }

    /**
     * Search post
     */ 
    #[OA\Tag(name:"Posts")]
    #[OA\Parameter(
        name:"id",
        in:"path",
        description:"Post ID",
        example : "1",
        required:false,
        schema: new OA\Schema(type:"integer" )
    )]
    #[OA\Response(
        response:"200",
        description:"Return post (application/json)",
        content: new OA\JsonContent(
        type: "object",
        properties: [
            new OA\Property(property: "status", type: "string", example: "success"),
            new OA\Property(
                property: "data",
                type: "array",
                items: new OA\Items(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "userId", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "Post title...")
                    ]
                )
            )
        ]
    )
    )]
    public function searchPostAction(?int $id)
    {
        $data = [];
        if ($this->postApi->getPosts($id)) {
            try {
                $posts = $this->postApi->getResult();
                $users = [];
                if ($this->postApi->getUsers()) {
                    $users = $this->postApi->getResult();
                }
                $data = $this->mergePostsUsers($posts, $users);
            } catch (\Throwable $e) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Failed to get data: ' . $e->getMessage()
                ], 500);
            }
        }
        return new JsonResponse([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Show posts
     */ 
    #[OA\Tag(name:"Posts")]
    #[OA\Response(
        response:"200",
        description:"Return list of posts (application/json)",
        content: new OA\JsonContent(
        type: "object",
        properties: [
            new OA\Property(property: "status", type: "string", example: "success"),
            new OA\Property(
                property: "data",
                type: "array",
                items: new OA\Items(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "userId", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "Post title...")
                    ]
                )
            )
        ]
    )
    )]
    public function showPostsAction()
    {
        $data = [];
        if ($this->postApi->getPosts()) {
            $posts = $this->postApi->getResult();
            $users = [];
            if ($this->postApi->getUsers()) {
                $users = $this->postApi->getResult();
            }
            // Merge posts with users
            $data = $this->mergePostsUsers($posts, $users);
        }
        
        return new JsonResponse([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    private function mergePostsUsers(array $posts, array $users)
    {
        $usersById = [];
        foreach ($users as $user) {
            $usersById[$user->getId()] = $user;
        }
        return array_map(function ($post) use ($usersById) {
            return [
                'id' => $post->getId(),
                'userId' => $post->getUserId(),
                'title' => $post->getTitle(),
                'body' => $post->getBody(),
                'user' => $usersById[$post->getUserId()] ?? null,
            ];
        }, $posts);
    }

    /**
     * Create post
     */
    #[OA\Tag(name: "Posts")]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            // required: ["nameuser", "username", "email", "title", "body"],
            required: ["userId", "title", "body"],
            properties: [
                new OA\Property(property: "nameUser", type: "string", example: "Leanne Graham"),
                new OA\Property(property: "userName", type: "string", example: "Bret"),
                new OA\Property(property: "email", type: "string", example: "Sincere@april.biz"),
                new OA\Property(property: "title", type: "string", example: "sunt aut facere repella"),
                new OA\Property(property: "body", type: "string", example: "quia et suscipit\nsuscipit recusandae consequuntur")
            ]
        )
    )]
    #[OA\Response(
        response: "201",
        description: "Post was created (application/json)",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "message", type: "string", example: "Post created successfully"),
                new OA\Property(property: "id", type: "integer", example: 42)
            ]
        )
    )]
    #[OA\Response(
        response:"400",
        description:"Wrong Content Type, allow: 'x-www-form-urlencoded' or 'json'"
    )]
    public function createPostAction(Request $request)
    {
        $contentType = $request->headers->get('content-type');
        try {
            if ($contentType === 'application/json') {
                $data = json_decode($request->getContent(), true);
            } elseif ($contentType === 'application/x-www-form-urlencoded') {
                $data = $request->request->all();
            } else {
                throw new BadRequestHttpException("Wrong Content Type, allowed: 'application/json' or 'application/x-www-form-urlencoded'");
            }
        } catch (\JsonException $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON: ' . $e->getMessage()], 400);
        }

        $dtoUser = new UserDto(
            id: 777,
            name: $data['nameUser'] ?? null,
            username: $data['userName'] ?? null,
            email: $data['email'] ?? null,
        );

        $dtoPost = new PostDto(
            id: 0,
            userId: 0,
            title: $data['title'] ?? null,
            body: $data['body'] ?? null,
        );

        if(!empty($dtoUser->getName())){
            $dtoPost->setUser($dtoUser);
        }

        // Validate
        try {
            $errors = $this->validator->validate($dtoPost);
        } catch (\Throwable $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Validation failed: ' . $e->getMessage()], 500);
        }
        
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return new JsonResponse(['status' => 'error', 'errors' => $errorMessages], 400);
        }

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Post created successfully',
            'data' => [
                'id' => 999,
                'title' => $dtoPost->getTitle(),
                'body' => $dtoPost->getBody(),
                'user' => $dtoPost->getUser()
            ],
        ], 201);
    }
}
