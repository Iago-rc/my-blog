<?php

namespace App\Tests\Controller;

use App\Tests\Service\PostApiStub;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostsControllerTest extends WebTestCase
{
    private PostApiStub $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = new PostApiStub();
    }

    public function testShowsAllPosts()
    {
        $result = $this->stub->getPosts();
        $this->assertTrue($result);
        $data = $this->stub->getResult();
        $titles = array_column($data, 'title');
        $this->assertContains('Primer post', $titles);
        $this->assertContains('Segundo post', $titles);
    }

    public function testPostByIdSuccess()
    {
        $success = $this->stub->getPosts(1);
        $this->assertTrue($success);
        $result = $this->stub->getResult();
        $this->assertIsArray($result);
        $post = reset($result); 
        $this->assertEquals(1, $post['id']);
        $this->assertEquals('Primer post', $post['title']);
    }

    public function testPostByIdNotFound()
    {
        $success = $this->stub->getPosts(777);
        $this->assertFalse($success);
        $this->assertEquals('Post con id 777 no encontrado', $this->stub->getError());
        $this->assertNull($this->stub->getResult());
    }

    public function testGetUsers()
    {
        $success = $this->stub->getUsers();
        $this->assertTrue($success);
        $users = $this->stub->getResult();
        $this->assertCount(2, $users);
        $this->assertEquals('Usuario 1', $users[0]['name']);
    }

    public function testUserByIdNotFound()
    {
        $success = $this->stub->getUsers(777);
        $this->assertFalse($success);
        $this->assertEquals('Usuario con id 777 no encontrado', $this->stub->getError());
        $this->assertNull($this->stub->getResult());
    }
}


