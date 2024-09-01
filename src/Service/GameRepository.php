<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Game;
use Symfony\Component\String\Slugger\AsciiSlugger;

class GameRepository
{
    private string $path;
    private AsciiSlugger $slugger;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->slugger = new AsciiSlugger();
    }

    public function create(Game $game): Game
    {
        $slug = $this->getSlug($game->getName());

        $game->setSlug($slug);

        $path = $this->getPath($slug);

        if (file_exists($path)) {
            throw new \Exception("Game with slug \"$slug\" already exists.");
        }

        $this->createPath($path);
        $this->writeConfigFile($path, $game->toArray());

        return $game;
    }

    public function update(Game $game, string $currentSlug): void
    {
        $slug = $game->getSlug();

        if ($currentSlug !== $slug) {
            try {
                rename(
                    $this->getPath($currentSlug),
                    $this->getPath($slug)
                );
            } catch (\Exception $exception) {
                throw new \Exception(\sprintf('Could not rename game  "%s" to "%s": %s', $currentSlug, $slug, $exception));
            }
        }

        $path = $this->getPath($slug);

        if (!file_exists($path)) {
            throw new \Exception("Could not find game \"$slug\".");
        }

        $this->writeConfigFile($path, $game->toArray());
    }

    public function getSlug(string $name): string
    {
        return strtolower((string) $this->slugger->slug($name));
    }

    public function isAvailable(string $slug): bool
    {
        return file_exists($this->getPath($slug));
    }

    private function createPath(string $path): void
    {
        mkdir($path);
    }

    /**
     * @param array<string, mixed> $info
     */
    private function writeConfigFile(string $path, array $info): void
    {
        file_put_contents(
            \sprintf('%s/info.json', $path),
            json_encode($info, JSON_PRETTY_PRINT)
        );
    }

    private function getPath(string $slug): string
    {
        return \sprintf('%s/%s', $this->path, $slug);
    }
}
