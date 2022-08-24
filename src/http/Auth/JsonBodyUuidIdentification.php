<?php

namespace Gb\Php2\http\Auth;

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Gb\Php2\http\Request;
use Gb\Php2\Exeptions\AuthException;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\Exeptions\UserNotFoundException;
use Gb\Php2\http\Auth\IdentificationInterface;
use Gb\Php2\Exeptions\InvalidArgumentException;
use Gb\Php2\Interfaces\UsersRepositoryInterface;

class JsonBodyUuidIdentification implements IdentificationInterface
{
    private UsersRepositoryInterface $usersRepository;

    public function __construct(UsersRepositoryInterface $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function user(Request $request): User
    {
        try {
            // Получаем UUID пользователя из JSON-тела запроса;
            // ожидаем, что корректный UUID находится в поле user_uuid
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            // Если невозможно получить UUID из запроса -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
            // Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            // Если пользователь с таким UUID не найден -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}
