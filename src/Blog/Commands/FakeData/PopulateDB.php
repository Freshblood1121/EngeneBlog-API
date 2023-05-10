<?php

namespace GeekBrains\LevelTwo\Blog\Commands\FakeData;

use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
// Внедряем генератор тестовых данных и
// репозитории пользователей и статей
    public function __construct(
        private \Faker\Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data');
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
// Создаём десять пользователей
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->username());
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

    /**
     * @throws InvalidArgumentException
     */
    private function createFakeUser(): User
    {
        $user = User::createFrom(
// Генерируем имя пользователя
            $this->faker->userName,
// Генерируем пароль
            $this->faker->password,
            new Name(
// Генерируем имя
                $this->faker->firstName,
// Генерируем фамилию
                $this->faker->lastName
            )
        );
// Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
    }

    /**
     * @throws InvalidArgumentException
     */
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