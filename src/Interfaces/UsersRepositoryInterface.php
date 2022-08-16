<?php
namespace Gb\Php2\Interfaces;

use Gb\Php2\Blog\User;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function getByUsername(string $user_name): User;
    
}