<?php

namespace Janisbiz\LightOrm\MySQL\Enums;

class JoinEnum
{
    const LEFT = 'LEFT JOIN';
    const INNER = 'INNER JOIN';
    const FULL_OUTER = 'FULL OUTER JOIN';
    const RIGHT = 'RIGHT JOIN';
    const CROSS = 'CROSS JOIN';
    const JOINS = [
        self::LEFT,
        self::INNER,
        self::FULL_OUTER,
        self::RIGHT,
        self::CROSS,
    ];
}
