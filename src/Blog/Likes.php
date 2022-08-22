<?php
namespace Gb\Php2\Blog;

class Likes 
{
    private UUID $uuid_likes;
    private UUID $uuid_post_likes;
    private UUID $uuid_user_likes;

    public function __construct(UUID $uuid_likes, Post $post, User $user)
    {
        $this->uuid_likes = $uuid_likes;
        $this->uuid_post_likes = $post->getUuid();
        $this->uuid_user_likes = $user->getUuid();
    }

    public function getUuid(): UUID
    {
        return $this->uuid_likes;
    }

    public function setUuid($uuid_likes)
    {
        $this->uuid_likes = $uuid_likes;

        return $this;
    }

    public function getUuidPostLikes(): UUID
    {
        return $this->uuid_post_likes;
    }

    public function setUuidPostLikes($uuid_post_likes)
    {
        $this->uuid_post_likes = $uuid_post_likes;

        return $this;
    }

    public function getUuidUserLikes(): UUID
    {
        return $this->uuid_user_likes;
    }

    public function setUuidUserLikes($uuid_user_likes)
    {
        $this->uuid_user_likes = $uuid_user_likes;

        return $this;
    }
}