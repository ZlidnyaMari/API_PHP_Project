<?php
namespace Gb\Php2\http\Actions\Posts;

use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\UUID;
use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\Interfaces\PostsRepositoryInterface;
use Gb\Php2\Interfaces\UsersRepositoryInterface;

class CreatePosts implements ActionInterface 
{
    private PostsRepositoryInterface $postsRepository;
    private UsersRepositoryInterface $userRepository;

    public function __construct(PostsRepositoryInterface $postsRepository, UsersRepositoryInterface $userRepository) 
    {
        $this->postsRepository = $postsRepository;
        $this->userRepository = $userRepository;
    }

    public function handle(Request $request): Response
    {
        try {
            $newPostUuid = UUID::random();
            $autorUser = ($request->jsonBodyField('autor'));
            $user = $this->userRepository->getByUsername($autorUser);

            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text')
        
            );

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());

        }

        $this->postsRepository->save($post);

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}  

