<?php
require_once __DIR__ . '/vendor/autoload.php';

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\Comment;
use Gb\Php2\Exeptions\CommentNotFoundException;
use Gb\Php2\Exeptions\PostNotFoundException;
use Gb\Php2\Exeptions\UserNotFoundException;
use Gb\Php2\Repositories\AddToSqliteUsersRepositories;
use Gb\Php2\Repositories\AddToSqlitePostRepositories;
use Gb\Php2\Repositories\AddToSqliteCommentRepositories;
try{

    $faker = Faker\Factory::create('ru_RU');

    $connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
    $usersRepository = new AddToSqliteUsersRepositories($connection);
    //$usersRepository->save(new User((UUID::random()), 'admin', 'Anna', 'German'));
    echo ($usersRepository->getByUsername('admin'));
    
    $articleRepository = new AddToSqlitePostRepositories($connection);
    $user = new User((UUID::random()), 'admin', 'Anna', 'German');
    //$articleRepository->save(new Post((UUID::random()), $user, 'Заголовок статьи', 'Текст статьи'));
    echo ($articleRepository->getPostByTitle('Заголовок статьи'));
    
    $commentRepository = new AddToSqliteCommentRepositories($connection);
    $article = new Post((UUID::random()), $user, 'Заголовок статьи', 'Текст статьи');
    //$commentRepository->save(new Comment((UUID::random()), $user, $article, 'Текст комментария' ));
    echo ($commentRepository->getCommentUuid('f5cb51be-22ef-4c43-8ea3-171e91c6a00b'));

} catch(UserNotFoundException $exeption){
    echo $exeption->getMessage();
} catch(PostNotFoundException $exeption){
    echo $exeption->getMessage();
} catch(CommentNotFoundException $exeption){
    echo $exeption->getMessage();
}    


