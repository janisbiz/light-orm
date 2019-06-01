<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\ColumnTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\CommandTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\TableTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\GroupByTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\HavingTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\JoinTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\LimitOffsetTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\OnDuplicateKeyUpdateTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\OrderByTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\SetTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\UnionTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\ValueTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\WhereTrait;

trait Traits
{
    /** Base traits */
    use BindTrait;

    /** MySQL specific traits */
    use CommandTrait;
    use JoinTrait;
    use WhereTrait;
    use OrderByTrait;
    use LimitOffsetTrait;
    use GroupByTrait;
    use SetTrait;
    use ValueTrait;
    use ColumnTrait;
    use TableTrait;
    use HavingTrait;
    use OnDuplicateKeyUpdateTrait;
    use UnionTrait;
}
