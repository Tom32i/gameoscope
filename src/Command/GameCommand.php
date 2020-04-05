<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\String\Slugger\AsciiSlugger;

class GameCommand extends Command
{
    protected static $defaultName = 'app:game';

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->slugger = new AsciiSlugger();

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Create a new game');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Ajout d\'un jeu !');

        $name = $io->ask('Nom du jeu');
        $slug = $io->ask('Slug', $this->getSlug($name));

        $path = sprintf('%s/%s', $this->path, $slug);

        if (\file_exists($path)) {
            $io->error("Le jeu \"${slug}\" existe déjà.");

            return 1;
        }

        $year = $io->ask('Année de sortie');
        $date = $io->ask('Date du publication', (new \DateTimeImmutable())->format('Y-m-d'));
        $studioName = $io->ask('Nom du studio');
        $studioUrl = $io->ask('Url du studio');

        $io->definitionList(
            ['Slug' => $slug],
            ['Nom' => $name],
            ['Année' => $year],
            ['Date' => $date],
            ['Studio' => sprintf('%s (%s)', $studioName, $studioUrl)],
        );

        if (!$io->confirm('Créer le jeu ?')) {
            return 0;
        }

        $io->note('Création du repertoir du jeu ...');
        $this->createPath($path);

        $io->note('Création du fichier de configuration ...');
        $this->createConfigFile($path, [
            'draft' => true,
            'name' => $name,
            'date' => $date,
            'year' => $year,
            'studio' => [
                'name' => $studioName,
                'url' => $studioUrl,
            ],
        ]);

        $io->success('Jeu créé!');

        return 0;
    }

    private function createPath(string $path)
    {
        mkdir($path);
    }

    private function createConfigFile(string $path, array $info)
    {
        file_put_contents(
            sprintf('%s/info.json', $path),
            json_encode($info, JSON_PRETTY_PRINT)
        );
    }

    private function getSlug(string $name): string
    {
        return strtolower($this->slugger->slug($name));
    }
}
