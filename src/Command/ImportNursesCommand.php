<?php

namespace App\Command;

use App\Entity\Nurse;
use App\Repository\NurseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:import-nurses', description: 'Imports nurses from data/nurses.json, hashing their passwords')]
class ImportNursesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NurseRepository $nurseRepository,
        #[Autowire('%kernel.project_dir%/data/nurses.json')]
        private string $filePath,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $nurses = is_file($this->filePath) ? json_decode(file_get_contents($this->filePath), true) : null;

        if (!is_array($nurses)) {
            $io->error('data/nurses.json not found or not valid');

            return Command::FAILURE;
        }

        $imported = 0;
        $skipped = 0;

        foreach ($io->progressIterate($nurses) as $data) {
            if ($this->nurseRepository->findOneBy(['email' => $data['email']])) {
                ++$skipped;
                continue;
            }

            $nurse = (new Nurse())
                ->setName($data['name'])
                ->setEmail($data['email'])
                ->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));

            $this->entityManager->persist($nurse);
            ++$imported;

            if (0 === $imported % 100) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('Imported %d nurses, skipped %d already in the database.', $imported, $skipped));

        return Command::SUCCESS;
    }
}
