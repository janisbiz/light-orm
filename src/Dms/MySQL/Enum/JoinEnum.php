<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Enum;

class JoinEnum
{
    const LEFT_JOIN = 'LEFT JOIN';
    const INNER_JOIN = 'INNER JOIN';
    const FULL_OUTER_JOIN = 'FULL OUTER JOIN';
    const RIGHT_JOIN = 'RIGHT JOIN';
    const CROSS_JOIN = 'CROSS JOIN';
    const JOINS = [
        self::LEFT_JOIN,
        self::INNER_JOIN,
        self::FULL_OUTER_JOIN,
        self::RIGHT_JOIN,
        self::CROSS_JOIN,
    ];
}
