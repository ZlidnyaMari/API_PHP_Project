<?php
require_once __DIR__ . '/vendor/autoload.php';

use Gb\Php2\Users\User;
use Gb\Php2\Comments\Comment;
use Gb\Php2\Articles\Article;

$faker = Faker\Factory::create('ru_RU');

// spl_autoload_register(function ($class) 
// {
//     $newClass = str_replace('\\', '/', $class);
//     $newClass = str_replace('_', '/', $newClass);
//     $newClass = str_replace('Gb/Php2/', 'src/', $newClass) . ".php";
//     var_dump($newClass);

// });

switch ($argv[1]) {
    case 'user':
        $user = new User(1, $faker->firstName(), $faker->lastName());
        echo($user->__toString());
        break;

    case 'post':
        $article = new Article(2, $faker->realText(rand(10, 20)), $faker->realText(rand(50, 100)));
        echo($article->__toString());
        break; 
        
    case 'comment':
        $comment = new Comment(3, $faker->realText(rand(50, 100)));
        echo($comment->getText());
        break;       
}


