<?php

namespace Janisbiz\LightOrm\MySQL\Enums;

class CommandEnum
{
    const INSERT_INTO = 'INSERT INTO';
    const INSERT_IGNORE_INTO = 'INSERT IGNORE INTO';
    const REPLACE_INTO = 'REPLACE INTO';
    const SELECT = 'SELECT';
    const UPDATE = 'UPDATE';
    const UPDATE_IGNORE = 'UPDATE IGNORE';
    const DELETE = 'DELETE';
    const COMMANDS = [
        self::INSERT_INTO,
        self::INSERT_IGNORE_INTO,
        self::REPLACE_INTO,
        self::SELECT,
        self::UPDATE,
        self::UPDATE_IGNORE,
        self::DELETE,
    ];
}
