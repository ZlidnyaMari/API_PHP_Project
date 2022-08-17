<?php
namespace Gb\Php2\http\Actions\Comment;

use Gb\Php2\Blog\UUID;
use Gb\Php2\Blog\Comment;
use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\Interfaces\PostsRepositoryInterface;
use Gb\Php2\Interfaces\UsersRepositoryInterface;
use Gb\Php2\Interfaces\CommentsRepositoryInterface;

class CreateComment implements ActionInterface
{
    private UsersRepositoryInterface $usersRepository;
    private PostsRepositoryInterface $postRepository;
    private CommentsRepositoryInterface $commentRepository;

    public function __construct(
    UsersRepositoryInterface $usersRepository,
    PostsRepositoryInterface $postRepository,
    CommentsRepositoryInterface $commentRepository    
    ) 
    {
        $this->usersRepository = $usersRepository;
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
    }

    public function handle(Request $request): Response
    {
        try {
            $newCommentUuid = UUID::random();

            $autorUser = ($request->jsonBodyField('autor'));
            $user = $this->usersRepository->getByUsername($autorUser);

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