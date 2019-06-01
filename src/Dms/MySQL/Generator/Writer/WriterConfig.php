<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Generator\Writer\AbstractWriterConfig;

class WriterConfig extends AbstractWriterConfig
{
    /**
     * @param string $directory
     * @param null|string $namespace
     * @param null|string $classPrefix
     * @param null|string $classSuffix
     */
    public function __construct(
        string $directory,
        ?string $namespace = '',
        ?string $classPrefix = '',
        ?string $classSuffix = ''
    ) {
        $this->directory = $directory;
        $this->namespace = $namespace;
        $this->classPrefix = $classPrefix;
        $this->classSuffix = $classSuffix;
    }
}
