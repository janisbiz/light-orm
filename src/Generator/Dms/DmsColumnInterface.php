<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Dms;

interface DmsColumnInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getPhpName(): string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getPhpType(): string;

    /**
     * @return bool
     */
    public function isNullable(): bool;

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @return string
     */
    public function getDefault(): string;

    /**
     * @return string
     */
    public function getPhpDefaultType(): string;

    /**
     * @return null|string
     */
    public function getExtra(): ?string;
}
