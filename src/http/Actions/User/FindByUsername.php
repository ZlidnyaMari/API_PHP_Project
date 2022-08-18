<?php

namespace Gb\Php2\http\Actions\User;

use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\Exeptions\UserNotFoundException;
use Gb\Php2\Interfaces\UsersRepositoryInterface;

// Класс реализует контракт действия
class FindByUsername implements ActionInterface
{
    private UsersRepositoryInterface $usersRepository;
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(UsersRepositoryInterface $usersRepository) 
    {
        $this->usersRepository = $usersRepository;
    }
    // Функция, описанная в контракте
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $username = $request->query('username');
        } catch (HttpException $e) {
            // Если в запросе нет параметра username -
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }
        try {
            // Пытаемся найти пользователя в репозитории
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            // Если пользователь не найден -
            // возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }
        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'username' => $user->getUser_name(),
            'name' => $user->getFirst_name() . ' ' . $user->getLast_name(),
        ]);
    }
}
