<?php

declare(strict_types=1);

namespace App\Model;

use Tom32i\ShowcaseBundle\Model\Group;

/**
 * @property Screenshot[] $images
 */
class Game extends Group
{
    /**
     * @param array{name: string, url: string} $studio
     */
    public static function create(
        string $slug,
        string $name,
        int $year,
        \DateTimeInterface $date,
        array $studio,
        bool $draft = true,
    ): self {
        $game = new self($slug);

        $game->setName($name);
        $game->setYear($year);
        $game->setDate($date);
        $game->setStudio($studio);
        $game->setDraft($draft);

        return $game;
    }

    public function getCover(): ?Screenshot
    {
        $images = array_filter($this->images, fn (Screenshot $image): bool => !$image->isSpoil());

        if (\count($images) > 0) {
            return reset($images);
        }

        if (\count($this->images) > 0) {
            return reset($this->images);
        }

        return null;
    }

    public function isDraft(): bool
    {
        if (isset($this->config['draft'])) {
            return (bool) $this->config['draft'];
        }

        return false;
    }

    public function setDraft(bool $draft = true): void
    {
        $this->config['draft'] = $draft;
    }

    public function getName(): string
    {
        return (string) $this->config['name'];
    }

    public function setName(string $name): void
    {
        $this->config['name'] = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getYear(): int
    {
        return (int) $this->config['year'];
    }

    public function setYear(int $year): void
    {
        $this->config['year'] = $year;
    }

    /**
     * @return array{name: string, url: string}
     */
    public function getStudio(): array
    {
        return $this->config['studio'];
    }

    /**
     * @param array{name: string, url: string} $studio
     */
    public function setStudio(array $studio): void
    {
        $this->config['studio'] = $studio;
    }

    public function getDate(): \DateTimeInterface
    {
        return new \DateTime((string) $this->config['date']);
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->config['date'] = $date->format('Y-m-d');
    }

    public function moveBy(Screenshot $screenshot, int $step): bool
    {
        $index = $this->getIndex($screenshot);
        $insertAt = max(0, $index + $step);

        if ($step > 0) {
            ++$insertAt;
        }

        return $this->move($index, $insertAt);
    }

    public function moveTop(Screenshot $screenshot): bool
    {
        return $this->move($this->getIndex($screenshot), 0);
    }

    public function moveBottom(Screenshot $screenshot): bool
    {
        return $this->move($this->getIndex($screenshot), \count($this->images));
    }

    private function move(int $from, int $to): bool
    {
        $images = $this->images;
        $screenshot = $images[$from];
        $images[$from] = null;
        array_splice($images, $to, 0, [$screenshot]);

        $this->images = array_values(array_filter($images));

        return true;
    }

    private function getIndex(Screenshot $screenshot): int
    {
        $index = array_search($screenshot, $this->images, true);

        if ($index === false || !\is_int($index)) {
            throw new \LogicException('Screenshot not found');
        }

        return $index;
    }

    public function remove(Screenshot $screenshot): void
    {
        $index = array_search($screenshot, $this->images, true);

        if ($index === false) {
            throw new \LogicException('Screenshot not found');
        }

        unset($this->images[$index]);
    }

    public function sortByDate(): void
    {
        $this->sortImages(fn ($a, $b): int => $a->compareDate($b));
    }

    public function sortBySlug(): void
    {
        $this->sortImages(fn ($a, $b): int => $a->compareName($b));
    }

    public function shuffle(): void
    {
        shuffle($this->images);
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return $this->config;
    }
}
