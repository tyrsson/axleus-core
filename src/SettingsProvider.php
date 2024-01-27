<?php

declare(strict_types=1);

namespace Axleus;

use function file_exists;

class SettingsProvider
{
    final public const SETTINGS_PATH = __DIR__ . '/../../../../data/settings/';
    /**
     * Extending class should define this property as the target file name
     */
    protected ?string $file = null;

    public function __invoke(): array
    {
        if ($this->file !== null && file_exists(self::SETTINGS_PATH . $this->file)) {
            return include_once self::SETTINGS_PATH . $this->file;
        }
        return [];
    }
}
