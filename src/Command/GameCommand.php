<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Game;
use App\Service\GameRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:game',
    description: 'Create a new game.',
    hidden: false,
)]
class GameCommand extends Command
{
    public function __construct(
        private GameRepository $repository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Ajout d\'un jeu !');

        $name = \strval($io->ask('Nom du jeu'));
        $slug = \strval($io->ask('Slug', $this->repository->getSlug($name)));

        if ($this->repository->isAvailable($slug)) {
            $io->error("Le jeu \"{$slug}\" existe déjà.");

            return Command::FAILURE;
        }

        $year = \intval($io->ask('Année de sortie'));
        $date = $io->ask('Date du publication', (new \DateTimeImmutable())->format('Y-m-d'));
        $studioName = \strval($io->ask('Nom du studio'));
        $studioUrl = \strval($io->ask('Url du studio'));

        $io->definitionList(
            ['Slug' => $slug],
            ['Nom' => $name],
            ['Année' => $year],
            ['Date' => $date],
            ['Studio' => \sprintf('%s (%s)', $studioName, $studioUrl)],
        );

        if (!$io->confirm('Créer le jeu ?')) {
            return Command::FAILURE;
        }

        $game = Game::create(
            $slug,
            $name,
            $year,
            $date,
            [
                'name' => $studioName,
                'url' => $studioUrl,
            ]
        );

        $io->note('Création du jeu ...');

        try {
            $this->repository->create($game);
        } catch (\Exception) {
            $io->error('Impossible de créer le jeu.');

            return Command::FAILURE;
        }

        $io->success('Jeu créé!');

        return Command::SUCCESS;
    }
}
