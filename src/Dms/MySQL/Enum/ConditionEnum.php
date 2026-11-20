<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Enum;

class ConditionEnum
{
    const string LIMIT = 'LIMIT';
    const string OFFSET = 'OFFSET';
    const string ORDER_BY = 'ORDER BY';
    const string GROUP_BY = 'GROUP BY';
    const string SET = 'SET';
    const string WHERE = 'WHERE';
    const string VALUES = 'VALUES';
    const string HAVING = 'HAVING';
    const string FROM = 'FROM';
}
