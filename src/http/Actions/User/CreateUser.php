<?php
namespace Gb\Php2\http\Actions\User;

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\Interfaces\UsersRepositoryInterface;

class CreateUser implements ActionInterface
{
    private UsersRepositoryInterface $usersRepository;

    public function __construct(UsersRepositoryInterface $usersRepository) 
    {
        $this->usersRepository = $usersRepository;
    }

    public function handle(Request $request): Response
    {
        try {
            $newUserUuid = UUID::random();

            $user = new User(
                $newUserUuid,
                $request->jsonBodyField('username'),
                $request->jsonBodyField('first_name'),
                $request->jsonBodyField('last_name')
        
            );

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());

        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid,
        ]);
    }
}
