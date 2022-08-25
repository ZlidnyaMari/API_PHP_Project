<?php
use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Gb\Php2\Blog\Comment;
use PHPUnit\Framework\TestCase;
use Gb\Php2\UnitTests\DummyLogger;
use Gb\Php2\Exeptions\CommentNotFoundException;
use Gb\Php2\Repositories\SqliteCommentRepositories;

class SqliteCommentRepositoryTest extends TestCase
{
    public function  testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentRepositories($connectionMock, new DummyLogger());
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('No such comment : f5cb51be-22ef-4c43-8ea3-171e91c6a00b');
        $repository->getCommentUuid('f5cb51be-22ef-4c43-8ea3-171e91c6a00b');
    }

    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once()) 
            ->method('execute') 
            ->with([ 
            ':uuid' => '790bd24d-5a4d-4ea2-855e-8990d5ca8c7b',
            ':post_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':autor_uuid' => 'f5cb51be-22ef-4c43-8ea3-171e91c6a00b',
            ':text' => 'text',
        ]);
       
        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteCommentRepositories($connectionStub, new DummyLogger());
        $user = new User(new UUID('f5cb51be-22ef-4c43-8ea3-171e91c6a00b'), 'ivan123', 'Ivan', 'Nikitin');

        $repository->save(
        new Comment( 
        new UUID('790bd24d-5a4d-4ea2-855e-8990d5ca8c7b'),
        $user,
        new Post(new UUID('123e4567-e89b-12d3-a456-426614174000'), $user, 'header', 'text' ), 
        'text')
        );
    }
}