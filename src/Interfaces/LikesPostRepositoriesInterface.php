<?php
namespace Gb\Php2\Interfaces;

use Gb\Php2\Blog\Likes;

interface LikesPostRepositoriesInterface
{
    public function save(Likes $like): void;
    public function likesLimit($postUuid, $userUuid);
    public function getByPostUuid(string $uuid): array;
}