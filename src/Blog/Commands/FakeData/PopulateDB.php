<?php

namespace Gb\Php2\Blog\Commands\FakeData;

use Faker\Generator;
use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Gb\Php2\Interfaces\PostsRepositoryInterface;
use Gb\Php2\Interfaces\UsersRepositoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    private Generator $faker;
    private UsersRepositoryInterface $usersRepository;
    private PostsRepositoryInterface $postsRepository;
    // Внедряем генератор тестовых данных и
    // репозитории пользователей и статей
    public function __construct(
        Generator $faker, 
        UsersRepositoryInterface $usersRepository,
        PostsRepositoryInterface $postsRepository) 
    {
        $this->faker = $faker;
        $this->usersRepository = $usersRepository;
        $this->postsRepository = $postsRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data');
    }
    
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        // Создаём десять пользователей
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getUser_name());
        }
        // От имени каждого пользователя
        // создаём по двадцать статей
        foreach ($users as $user) {
            for ($i = 0; $i < 20; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getTitle());
            }
        }
        return Command::SUCCESS;
    }
    private function createFakeUser(): User
    {
        $user = User::createFrom(
            // Генерируем имя пользователя
            $this->faker->userName,
            // Генерируем пароль
            $this->faker->password,
            // Генерируем имя
            $this->faker->firstName,
            // Генерируем фамилию
            $this->faker->lastName
        );
        
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
    }

    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            // Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
            // Генерируем текст
            $this->faker->realText
        );
        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }
}
