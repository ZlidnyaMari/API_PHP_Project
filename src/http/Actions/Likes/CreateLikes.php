<?php
namespace Gb\Php2\http\Actions\Likes;

use Gb\Php2\Blog\UUID;
use Gb\Php2\Blog\Likes;
use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\AppException;
use Gb\Php2\Exeptions\AuthException;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\Exeptions\LikesLimitExeption;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\Interfaces\PostsRepositoryInterface;
use Gb\Php2\Interfaces\UsersRepositoryInterface;
use Gb\Php2\http\Auth\TokenAuthenticationInterface;
use Gb\Php2\Interfaces\LikesPostRepositoriesInterface;

class CreateLikes implements ActionInterface
{
    private PostsRepositoryInterface $postsRepository;
    private UsersRepositoryInterface $userRepository;
    private LikesPostRepositoriesInterface $likesPostRepository;
    private TokenAuthenticationInterface $authentication;

    public function __construct(
        PostsRepositoryInterface $postsRepository, 
        UsersRepositoryInterface $userRepository,
        LikesPostRepositoriesInterface $likesPostRepository,
        TokenAuthenticationInterface $authentication) 
    {
        $this->postsRepository = $postsRepository;
        $this->userRepository = $userRepository;
        $this->likesPostRepository = $likesPostRepository;
        $this->authentication = $authentication;
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = ($request->jsonBodyField('post_uuid'));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage()); 
        }
        
        // try {
        //     $this->likesPostRepository->likesLimit($postUuid, $user);
        // } catch (LikesLimitExeption $e) {
        //     return new ErrorResponse($e->getMessage());
        // }    

        try {
            $newLikesUuid = UUID::random();
            $post = $this->postsRepository->get(new UUID($postUuid));
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
