<?php
namespace Gb\Php2\Interfaces;

use Gb\Php2\Blog\Post;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function getPostByTitle(string $title): Post;
    public function deletePostByTitle(string $title);
}