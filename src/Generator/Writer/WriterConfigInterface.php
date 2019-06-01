<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Writer;

interface WriterConfigInterface
{
    /**
     * @return string
     */
    public function getDirectory(): string;

    /**
     * @return null|string
     */
    public function getNamespace(): ?string;

    /**
     * @return null|string
     */
    public function getClassPrefix(): ?string;

    /**
     * @return null|string
     */
    public function getClassSuffix(): ?string;
}
