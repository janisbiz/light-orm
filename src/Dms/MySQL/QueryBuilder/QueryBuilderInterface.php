<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface as BaseQueryBuilderInterface;

interface QueryBuilderInterface extends BaseQueryBuilderInterface, TraitsInterface
{
    public function insert(bool $toString = false): string|EntityInterface;

    public function insertIgnore(bool $toString = false): string|EntityInterface;

    public function replace(bool $toString = false): string|EntityInterface;

    public function find(bool $toString = false): string|array;

    public function findOne(bool $toString = false): string|EntityInterface|null;

    public function update(bool $toString = false): string|EntityInterface|bool;

    public function updateIgnore(bool $toString = false): string|EntityInterface;

    public function delete(bool $toString = false): string|bool;

    public function count(bool $toString = false): string|int;
}
