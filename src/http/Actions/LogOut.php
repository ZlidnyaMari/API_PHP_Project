<?php
namespace Gb\Php2\http\Actions;

use DateTimeImmutable;
use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Gb\Php2\Exeptions\AuthException;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\Exeptions\AuthTokenNotFoundException;
use Gb\Php2\Interfaces\AuthTokensRepositoryInterface;

class LogOut implements ActionInterface
{
    // Репозиторий токенов
    private AuthTokensRepositoryInterface $authTokensRepository;
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(AuthTokensRepositoryInterface $authTokensRepository) 
    {
        $this->authTokensRepository = $authTokensRepository;
    }

    public function handle(Request $request): Response
    {
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
        
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        $authToken->setExpiresOn(new DateTimeImmutable());    
        
        $this->authTokensRepository->save($authToken); 

        return new SuccessfulResponse([
            'token' => (string)$authToken->token(),
        ]);
    }
}