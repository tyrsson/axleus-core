<?php

declare(strict_types=1);

namespace Axleus;

final class SettingsProvider
{
    /**
     * Return all non editable settings for application/module
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'axleus-key' => 'axleus-value',
        ];
    }
}
