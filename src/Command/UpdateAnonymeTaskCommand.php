<?php

namespace App\Command;

use App\Entity\Task;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'task-anonymous',
    description: 'Attribue les tâches sans utilisateur à l\'utilisateur anonyme',
)]
class UpdateAnonymeTaskCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em,
                                private readonly UserPasswordHasherInterface
                                $passwordHasher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Attribue les tâches sans utilisateur à l\'utilisateur anonyme');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $anonymeUser = $this->em->getRepository(Users::class)->findOneBy(['username' => 'Anonyme']);

        if(!$anonymeUser){
            $anonymeUser = new Users();
            $anonymeUser->setUsername('Anonyme')
                        ->setPassword($this->passwordHasher->hashPassword
                        ($anonymeUser, bin2hex(random_bytes(60))))
                        ->setEmail('anonyme@0829728729.com')
                        ->setRoles(['ROLE_ANONYME']);

            $this->em->persist($anonymeUser);

            $io->success('Utilisateur anonyme créé avec succès');
        }

        $tasks = $this->em->getRepository(Task::class)->findBy(['User' =>
            null]);

        foreach($tasks as $task){
            $task->setUser($anonymeUser);
        }

        $this->em->flush();

        $io->success('Tâches sans user attribuées à l\'utilisateur anonyme avec succès');

        return Command::SUCCESS;
    }
}
