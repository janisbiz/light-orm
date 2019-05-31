<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits\AbstractTraitTestCase;

class TraitsTest extends AbstractTraitTestCase
{
    public function test()
    {
        $traitsClass = new class () {
            use Traits;
        };
        
        $this->assertObjectUsesTrait(Traits::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\BindTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\ColumnTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\CommandTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\TableTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\GroupByTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\HavingTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\JoinTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\LimitOffsetTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\OnDuplicateKeyUpdateTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\OrderByTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\SetTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\UnionTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\ValueTrait::class, $traitsClass);
        $this->assertObjectUsesTrait(Traits\WhereTrait::class, $traitsClass);
    }
}
