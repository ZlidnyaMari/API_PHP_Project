<?php
namespace Gb\Php2\Interfaces;

use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
    public function getPostByTitle(string $title): Post;
    public function deletePostByTitle(string $title);
}