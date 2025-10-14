<?php

namespace App\Controller;

use App\Service\PostApiInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostsController extends AbstractController
{
    private const POSTS = 'posts';
    private const USER = 'user';

    #[Route('/posts', name: 'app_posts')]
    public function index(PostApiInterface $postApi): Response
    {
        try {
            $posts = [];
            if ($postApi->getPosts()) {
                $posts = $postApi->getResult() ?? [];
            }
        } catch (\Throwable $e) {
            $posts = [];
        }

        return $this->render('posts/index.html.twig', [
            self::POSTS => $posts,
        ]);
    }

    #[Route('/posts/user/{id}', name: 'app_user')]
    public function userAction(int $id, PostApiInterface $postApi): Response
    {
        try {
            $user = [];
            if ($postApi->getUsers($id)) {
                $user = $postApi->getResult() ?? [];
            }
        } catch (\Throwable $e) {
            $user = [];
        }
       
        return $this->render('posts/user.html.twig', [
            self::USER => $user,
        ]);
    }
}
