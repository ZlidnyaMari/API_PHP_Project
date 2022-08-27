<?php
namespace Gb\Php2\http\Actions\Comment;

use Gb\Php2\Blog\UUID;
use Gb\Php2\Blog\Comment;
use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\AuthException;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\Interfaces\PostsRepositoryInterface;
use Gb\Php2\http\Auth\TokenAuthenticationInterface;
use Gb\Php2\Interfaces\CommentsRepositoryInterface;

class CreateComment implements ActionInterface
{
    private PostsRepositoryInterface $postRepository;
    private CommentsRepositoryInterface $commentRepository;
    private TokenAuthenticationInterface $authentication;

    public function __construct(
    PostsRepositoryInterface $postRepository,
    CommentsRepositoryInterface $commentRepository,  
    TokenAuthenticationInterface $authentication  
    ) 
    {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->authentication = $authentication;
    }

    public function handle(Request $request): Response
    {
        try {
            $newCommentUuid = UUID::random();

            try {
                $user = $this->authentication->user($request);
            } catch (AuthException $e) {
                return new ErrorResponse($e->getMessage());
            }

            $titlePost = ($request->jsonBodyField('title'));
            $post = $this->postRepository->getPostByTitle($titlePost);

            $comment = new Comment(
                $newCommentUuid,
                $user,
                $post,
                $request->jsonBodyField('text')
        
            );

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());

        }

        $this->commentRepository->save($comment);

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);

    }
}