<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Dms;

interface DmsColumnInterface
{
    public function getName(): string;

    public function getPhpName(): string;

    public function getType(): string;

    public function getPhpType(): string;

    public function isNullable(): bool;

    public function getKey(): string;

    public function getDefault(): string;

    public function getPhpDefaultType(): string;

    public function getExtra(): ?string;
}
