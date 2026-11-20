<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Writer;

interface WriterConfigInterface
{
    public function getDirectory(): string;

    public function getNamespace(): ?string;

    public function getClassPrefix(): ?string;

    public function getClassSuffix(): ?string;
}
