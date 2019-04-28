<?php

namespace Janisbiz\LightOrm\MySQL\Traits;

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
