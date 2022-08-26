<?php

namespace Gb\Php2\http\Auth;

use Gb\Php2\Blog\User;
use Gb\Php2\http\Request;
use Gb\Php2\Exeptions\AuthException;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\Exeptions\UserNotFoundException;
use Gb\Php2\Interfaces\UsersRepositoryInterface;
use Gb\Php2\http\Auth\PasswordAuthenticationInterface;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    private UsersRepositoryInterface $usersRepository;

    public function __construct(UsersRepositoryInterface $usersRepository) 
    {
        $this->usersRepository = $usersRepository;
    }
    
    public function user(Request $request): User
    {
        // 1. Идентифицируем пользователя
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
        // 2. Аутентифицируем пользователя
        // Проверяем, что предъявленный пароль
        // соответствует сохранённому в БД
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }


        if (!$user->checkPassword($password)) {
            // Если пароли не совпадают — бросаем исключение
            throw new AuthException('Wrong password');
        }
        // Пользователь аутентифицирован
        return $user;
    }
}
