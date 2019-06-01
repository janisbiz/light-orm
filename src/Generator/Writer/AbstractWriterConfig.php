<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Writer;

abstract class AbstractWriterConfig implements WriterConfigInterface
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var null|string
     */
    protected $namespace = '';

    /**
     * @var null|string
     */
    protected $classPrefix = '';

    /**
     * @var null|string
     */
    protected $classSuffix = '';

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return \rtrim($this->directory, DIRECTORY_SEPARATOR);
    }

    /**
     * @return null|string
     */
    public function getNamespace(): ?string
    {
        return \trim($this->namespace, '\\');
    }

    /**
     * @return null|string
     */
    public function getClassPrefix(): ?string
    {
        return \mb_convert_case(\trim($this->classPrefix), MB_CASE_TITLE);
    }

    /**
     * @return null|string
     */
    public function getClassSuffix(): ?string
    {
        return \mb_convert_case(\trim($this->classSuffix), MB_CASE_TITLE);
    }
}
