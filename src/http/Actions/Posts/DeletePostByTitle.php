<?php

namespace Gb\Php2\http\Actions\Posts;

use Gb\Php2\http\Request;
use Gb\Php2\http\Response;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\http\Actions\ActionInterface;
use Gb\Php2\Interfaces\PostsRepositoryInterface;

class DeletePostByTitle implements ActionInterface
{
    private PostsRepositoryInterface $postRepository;

    public function __construct(PostsRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function handle(Request $request): Response
    {
        try {
            $titlePost = $request->query('title');

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $this->postRepository->deletePostByTitle($titlePost);


        return new SuccessfulResponse([
            'post' => $titlePost,
        ]);
    }
}
