<?php

namespace Janisbiz\LightOrm\Generator\Writer;

abstract class AbstractWriterConfig implements WriterConfigInterface
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $classPrefix = '';

    /**
     * @var string
     */
    protected $classSuffix = '';

    /**
     * @return string
     */
    public function getDirectory()
    {
        return \rtrim($this->directory, DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return \trim($this->namespace, '\\');
    }

    /**
     * @return string
     */
    public function getClassPrefix()
    {
        return \trim($this->classPrefix);
    }

    /**
     * @return string
     */
    public function getClassSuffix()
    {
        return \trim($this->classSuffix);
    }
}
