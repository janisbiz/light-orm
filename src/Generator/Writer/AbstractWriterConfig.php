<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Writer;

abstract class AbstractWriterConfig implements WriterConfigInterface
{
    public function __construct(
        protected string $directory,
        protected ?string $namespace = '',
        protected ?string $classPrefix = '',
        protected ?string $classSuffix = ''
    ) {
    }

    public function getDirectory(): string
    {
        return rtrim($this->directory, DIRECTORY_SEPARATOR);
    }

    public function getNamespace(): ?string
    {
        return trim($this->namespace, '\\');
    }

    public function getClassPrefix(): ?string
    {
        return mb_convert_case(trim($this->classPrefix), MB_CASE_TITLE);
    }

    public function getClassSuffix(): ?string
    {
        return mb_convert_case(trim($this->classSuffix), MB_CASE_TITLE);
    }
}
