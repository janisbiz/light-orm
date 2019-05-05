<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\OnDuplicateKeyUpdateTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\ValueTrait;

class OnDuplicateKeyUpdateTraitTest extends AbstractTraitTestCase
{
    use ValueTrait;
    use OnDuplicateKeyUpdateTrait;


    const VALUE_BIND_DEFAULT = [
        'Column1_Value' => 'value1',
    ];

    public function testOnDuplicateKeyUpdate()
    {
    }
}
