<?php

namespace Janisbiz\LightOrm\Generator\Writer;

interface WriterConfigInterface
{
    /**
     * @return string
     */
    public function getDirectory();

    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @return string
     */
    public function getClassPrefix();

    /**
     * @return string
     */
    public function getClassSuffix();
}
