<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits\AbstractTraitTest;

class TraitsTest extends AbstractTraitTest
{
    use Traits;
    
    public function test()
    {
        $this->assertObjectUsesTrait(Traits::class, $this);
        $this->assertObjectUsesTrait(Traits\BindTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\ColumnTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\CommandTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\FromTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\GroupTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\HavingTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\JoinTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\LimitOffsetTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\OnDuplicateKeyUpdateTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\OrderTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\SetTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\UnionTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\ValueTrait::class, $this);
        $this->assertObjectUsesTrait(Traits\WhereTrait::class, $this);
    }
}