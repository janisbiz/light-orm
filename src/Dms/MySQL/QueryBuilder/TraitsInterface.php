<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\BindTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\ColumnTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\CommandTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\TableTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\GroupByTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\HavingTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\JoinTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\LimitOffsetTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\OnDuplicateKeyUpdateTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\OrderByTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\SetTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\UnionTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\ValueTraitInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces\WhereTraitInterface;

interface TraitsInterface extends
    BindTraitInterface,
    ColumnTraitInterface,
    CommandTraitInterface,
    TableTraitInterface,
    GroupByTraitInterface,
    HavingTraitInterface,
    JoinTraitInterface,
    LimitOffsetTraitInterface,
    OnDuplicateKeyUpdateTraitInterface,
    OrderByTraitInterface,
    SetTraitInterface,
    UnionTraitInterface,
    ValueTraitInterface,
    WhereTraitInterface
{
}
