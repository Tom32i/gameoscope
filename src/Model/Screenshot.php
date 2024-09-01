<?php

declare(strict_types=1);

namespace App\Model;

use Tom32i\ShowcaseBundle\Model\Group;
use Tom32i\ShowcaseBundle\Model\Image;

/**
 * @property Game $group
 */
class Screenshot extends Image
{
    public const string SPOIL = 'Spoil';
    public const string CAPTURE = 'Capture';

    /**
     * @param array<string, mixed>  $exif
     * @param array<string, string> $props
     */
    public function __construct(
        Group $group,
        string $slug,
        \DateTimeImmutable $date,
        array $exif = [],
        array $props = [],
    ) {
        $date = new \DateTimeImmutable($props[self::CAPTURE] ?? $props['date:modify'] ?? $date->format('Y-m-d H:i:s'));

        parent::__construct($group, $slug, $date, $exif, array_merge($props, [self::CAPTURE => $date->format('Y-m-d H:i:s')]));
    }

    public function isSpoil(): bool
    {
        return ($this->props[self::SPOIL] ?? '') === self::SPOIL;
    }

    public function setSpoil(bool $spoil): void
    {
        $this->props[self::SPOIL] = $spoil ? self::SPOIL : '';
    }

    public function getGame(): Game
    {
        return $this->group;
    }

    public function move(int $step): bool
    {
        return $this->group->move($this, $step);
    }

    public function compareDate(self $image): int
    {
        return $this->date > $image->getDate() ? 1 : -1;
    }

    public function compareSlug(self $image): int
    {
        return strcmp($this->slug, $image->getSlug());
    }
}
