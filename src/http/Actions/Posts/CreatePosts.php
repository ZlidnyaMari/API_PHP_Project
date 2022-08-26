<?php

namespace Gb\Php2\http\Actions\Posts;

use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\UUID;
use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Psr\Log\LoggerInterface;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\AuthException;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\http\Auth\AuthenticationInterface;
use Gb\Php2\Interfaces\PostsRepositoryInterface;
use Gb\Php2\http\Auth\TokenAuthenticationInterface;

class CreatePosts implements ActionInterface
{
    private PostsRepositoryInterface $postsRepository;
    private LoggerInterface $logger;
    private TokenAuthenticationInterface $authentication;


    public function __construct(
        PostsRepositoryInterface $postsRepository,
        LoggerInterface $logger,
        TokenAuthenticationInterface $authentication
    ) {
        $this->postsRepository = $postsRepository;
        $this->logger = $logger;
        $this->authentication = $authentication;
    }

    public function handle(Request $request): Response
    {
        try {

            try {
                $user = $this->authentication->user($request);
            } catch (AuthException $e) {
                return new ErrorResponse($e->getMessage());
            }

            $newPostUuid = UUID::random();

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
        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}
