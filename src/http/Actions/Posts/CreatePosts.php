<?php
namespace Gb\Php2\http\Actions\Posts;

use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\UUID;
use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Psr\Log\LoggerInterface;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\http\Auth\IdentificationInterface;
use Gb\Php2\http\Auth\JsonBodyUsernameIdentification;
use Gb\Php2\Interfaces\PostsRepositoryInterface;

class CreatePosts implements ActionInterface 
{
    private PostsRepositoryInterface $postsRepository;
    private LoggerInterface $logger;
    private JsonBodyUsernameIdentification $identification;

    public function __construct(
        PostsRepositoryInterface $postsRepository, 
        LoggerInterface $logger,
        JsonBodyUsernameIdentification $identification
        ) 
    {
        $this->postsRepository = $postsRepository;
        $this->logger = $logger;
        $this->identification = $identification;
    }

    public function handle(Request $request): Response
    {
        try {
            $newPostUuid = UUID::random();
            $user = $this->identification->user($request);
            // $autorUser = ($request->jsonBodyField('autor'));
            // $user = $this->userRepository->getByUsername($autorUser);

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

