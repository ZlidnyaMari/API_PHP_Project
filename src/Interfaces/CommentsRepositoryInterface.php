<?php
namespace Gb\Php2\Interfaces;

use Gb\Php2\Blog\Comment;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;

    public function getCommentUuid(string $uuid): Comment;
}