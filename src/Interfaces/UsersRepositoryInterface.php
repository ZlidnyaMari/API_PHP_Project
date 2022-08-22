<?php
namespace Gb\Php2\Interfaces;

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $user_name): User;
    
}