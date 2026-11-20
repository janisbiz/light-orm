<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Enum;

class JoinEnum
{
    const string LEFT_JOIN = 'LEFT JOIN';
    const string INNER_JOIN = 'INNER JOIN';
    const string FULL_OUTER_JOIN = 'FULL OUTER JOIN';
    const string RIGHT_JOIN = 'RIGHT JOIN';
    const string CROSS_JOIN = 'CROSS JOIN';
    const array JOINS = [
        self::LEFT_JOIN,
        self::INNER_JOIN,
        self::FULL_OUTER_JOIN,
        self::RIGHT_JOIN,
        self::CROSS_JOIN,
    ];
}
