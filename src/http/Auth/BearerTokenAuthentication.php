<?php

namespace Gb\Php2\http\Auth;

use DateTimeImmutable;
use Gb\Php2\Blog\User;
use Gb\Php2\http\Request;
use Gb\Php2\Exeptions\AuthException;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\Interfaces\UsersRepositoryInterface;
use Gb\Php2\Exeptions\AuthTokenNotFoundException;
use Gb\Php2\http\Auth\TokenAuthenticationInterface;
use Gb\Php2\Interfaces\AuthTokensRepositoryInterface;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private AuthTokensRepositoryInterface $authTokensRepository;
    private UsersRepositoryInterface $usersRepository;
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        AuthTokensRepositoryInterface $authTokensRepository,
        UsersRepositoryInterface $usersRepository) 
    {
        $this->authTokensRepository = $authTokensRepository;
        $this->usersRepository = $usersRepository;

    }
    
    public function user(Request $request): User
    {
        // Получаем HTTP-заголовок
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        // Отрезаем префикс Bearer
        $token = mb_substr($header, strlen(self::HEADER_PREFIX));
        // Ищем токен в репозитории
        
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }
        
        // Проверяем срок годности токена
        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }

        // Получаем UUID пользователя из токена
        $userUuid = $authToken->userUuid();
        
        // Ищем и возвращаем пользователя
        return $this->usersRepository->get($userUuid);
    }
}
