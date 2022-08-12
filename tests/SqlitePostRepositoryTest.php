<?php

use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use PHPUnit\Framework\TestCase;
use Gb\Php2\Exeptions\PostNotFoundException;
use Gb\Php2\Repositories\SqlitePostRepositories;

class SqlitePostRepositoryTest extends TestCase
{
    public function  testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostRepositories($connectionMock);
        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('No such header : header');
        $repository->getPostByTitle('header');

    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once()) 
            ->method('execute') 
            ->with([ 
            ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':autor_uuid' => 'f5cb51be-22ef-4c43-8ea3-171e91c6a00b',
            ':title' => 'header',
            ':text' => 'text',
        ]);
       
        $connectionStub->method('prepare')->willReturn($statementMock);
        
        $repository = new SqlitePostRepositories($connectionStub);
        $repository->save(
        new Post( 
        new UUID('123e4567-e89b-12d3-a456-426614174000'),
        new User(new UUID('f5cb51be-22ef-4c43-8ea3-171e91c6a00b'), 'ivan123', 'Ivan', 'Nikitin'),
        'header', 
        'text')
        );
    }


}