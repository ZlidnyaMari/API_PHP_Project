<?php
namespace Gb\Php2\http\Actions;

use DateTimeImmutable;
use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Gb\Php2\Blog\AuthToken;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\AuthException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\Interfaces\AuthTokensRepositoryInterface;
use Gb\Php2\http\Auth\PasswordAuthenticationInterface;

class LogIn implements ActionInterface
{
    // Авторизация по паролю
    private PasswordAuthenticationInterface $passwordAuthentication;
    // Репозиторий токенов
    private AuthTokensRepositoryInterface $authTokensRepository;
    
    public function __construct(
        PasswordAuthenticationInterface $passwordAuthentication,
        AuthTokensRepositoryInterface $authTokensRepository) 
    {
        $this->passwordAuthentication = $passwordAuthentication;
        $this->authTokensRepository = $authTokensRepository;
    }
    
    public function handle(Request $request): Response
    {
        // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Генерируем токен
        $authToken = new AuthToken(
            // Случайная строка длиной 40 символов
            bin2hex(random_bytes(40)),
            $user->getUuid(),
            // Срок годности - 1 день
            (new DateTimeImmutable())->modify('+1 day')
        );
        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
        // Возвращаем токен
        return new SuccessfulResponse([
            'token' => (string)$authToken->token(),
        ]);
    }
}
