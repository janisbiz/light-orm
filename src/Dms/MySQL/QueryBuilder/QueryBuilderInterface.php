<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface as BaseQueryBuilderInterface;

interface QueryBuilderInterface extends BaseQueryBuilderInterface, TraitsInterface
{
    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function insert(bool $toString = false);
    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function insertIgnore(bool $toString = false);
    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function replace(bool $toString = false);

    /**
     * @param bool $toString
     *
     * @return string|EntityInterface[]
     */
    public function find(bool $toString = false);

    /**
     * @param bool $toString
     *
     * @return null|string|EntityInterface
     */
    public function findOne(bool $toString = false);

    /**
     * @param bool $toString
     *
     * @return bool|string|EntityInterface
     */
    public function update(bool $toString = false);

    /**
     * @param bool $toString
     *
     * @return bool|string|EntityInterface
     */
    public function updateIgnore(bool $toString = false);

    /**
     * @param bool $toString
     *
     * @return string|bool
     */
    public function delete(bool $toString = false);

    /**
     * @param bool $toString
     *
     * @return int
     */
    public function count(bool $toString = false);
}
