<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Enum;

class CommandEnum
{
    const string INSERT_INTO = 'INSERT INTO';
    const string INSERT_IGNORE_INTO = 'INSERT IGNORE INTO';
    const string REPLACE_INTO = 'REPLACE INTO';
    const string SELECT = 'SELECT';
    const string UPDATE = 'UPDATE';
    const string UPDATE_IGNORE = 'UPDATE IGNORE';
    const string DELETE = 'DELETE';
    const array COMMANDS = [
        self::INSERT_INTO,
        self::INSERT_IGNORE_INTO,
        self::REPLACE_INTO,
        self::SELECT,
        self::UPDATE,
        self::UPDATE_IGNORE,
        self::DELETE,
    ];
}
