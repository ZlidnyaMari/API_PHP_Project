<?php
namespace Gb\Php2\http\Actions\Likes;

use Gb\Php2\Blog\UUID;
use Gb\Php2\Blog\Likes;
use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\AppException;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\Exeptions\LikesLimitExeption;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\Interfaces\PostsRepositoryInterface;
use Gb\Php2\Interfaces\UsersRepositoryInterface;
use Gb\Php2\Interfaces\LikesPostRepositoriesInterface;

class CreateLikes implements ActionInterface
{
    private PostsRepositoryInterface $postsRepository;
    private UsersRepositoryInterface $userRepository;
    private LikesPostRepositoriesInterface $likesPostRepository;

    public function __construct(
        PostsRepositoryInterface $postsRepository, 
        UsersRepositoryInterface $userRepository,
        LikesPostRepositoriesInterface $likesPostRepository) 
    {
        $this->postsRepository = $postsRepository;
        $this->userRepository = $userRepository;
        $this->likesPostRepository = $likesPostRepository;
    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = ($request->jsonBodyField('post_uuid'));
            $userUuid = ($request->jsonBodyField('autor_uuid'));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage()); 
        }
        
        try {
            $this->likesPostRepository->likesLimit($postUuid, $userUuid);
        } catch (LikesLimitExeption $e) {
            return new ErrorResponse($e->getMessage());
        }    

        try {
            $newLikesUuid = UUID::random();
            $post = $this->postsRepository->get(new UUID($postUuid));
            $user = $this->userRepository->get(new UUID($userUuid));
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $likes = new Likes(
            $newLikesUuid,
            $post,
            $user
        );

        $this->likesPostRepository->save($likes);

        return new SuccessfulResponse([
            'uuid' => (string)$newLikesUuid,
        ]);
    }
}
