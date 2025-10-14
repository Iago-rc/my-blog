## My blog app 
Author: Iago Rodríguez Correia

## Information

| Item               | Versión            
| ------------------ | ------------------   |
| Platform           | Docker version 27.2.0|
| Language           | PHP 8.2.29           |
| PHP Framework      | Symfony 7.3.4        |
| Npm                | 2.15.8               |
| Yarn               | 1.22.22              |
| PhpUnit            | 11.5.42              |

## Structure

# The project is built on a docker container
folder: ../docker/docker-compose.yml

# We have two accessible routes in the controllers (2 sections)
/posts
/posts/user/{id}

# To run the assets I have used Webpack (from outside the container), on which Bootstrap has been installed
bundle used: "symfony/webpack-encore-bundle": "^2.3",
command: yarn watch

# An OpenAPI is used
bundle used: "nelmio/api-doc-bundle": "^5.6",
route: /api/doc/

# To make calls to the API, i have exposed the URLs:
[GET] /api/posts
[GET] /api/posts/1
[POST] /api/posts/

# To run unit tests exec:
php bin/phpunit tests/Controller/PostsControllerTest.php




