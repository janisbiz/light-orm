<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\ColumnTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\CommandTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\FromTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\GroupTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\HavingTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\JoinTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\LimitOffsetTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\OnDuplicateKeyUpdateTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\OrderTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\SetTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\UnionTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\ValueTrait;
use Janisbiz\LightOrm\Dms\MySQL\Traits\WhereTrait;

trait Traits
{
    /** Base traits */
    use BindTrait;

    /** MySQL specific traits */
    use CommandTrait;
    use JoinTrait;
    use WhereTrait;
    use OrderTrait;
    use LimitOffsetTrait;
    use GroupTrait;
    use SetTrait;
    use ValueTrait;
    use ColumnTrait;
    use FromTrait;
    use HavingTrait;
    use OnDuplicateKeyUpdateTrait;
    use UnionTrait;
}
