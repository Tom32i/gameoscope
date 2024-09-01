<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Screenshot;
use Tom32i\ShowcaseBundle\Behavior\Properties;

class ScreenshotRepository
{
    public function __construct(
        private string $path,
        private Properties $properties,
    ) {
    }

    public function save(Screenshot $screenshot): bool
    {
        return $this->properties->setAll(
            $this->getPath($screenshot),
            $screenshot->getProps()
        );
    }

    public function delete(Screenshot $screenshot): bool
    {
        try {
            unlink($this->getPath($screenshot));
        } catch (\Exception) {
            return false;
        }

        $screenshot->getGame()->remove($screenshot);

        return true;
    }

    private function getPath(Screenshot $screenshot): string
    {
        return \sprintf('%s/%s', $this->path, $screenshot->getPath());
    }
}
