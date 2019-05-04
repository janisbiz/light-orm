<?php

namespace Janisbiz\LightOrm\Connection;

/**
 * phpcs:disable
 *
 * @method \PDOStatement|bool prepare($statement, array $driver_options = array())
 * @method bool commit()
 * @method bool rollBack()
 * @method bool inTransaction()
 * @method bool setAttribute($attribute, $value)
 * @method int exec($statement)
 * @method \PDOStatement|false query($statement, $mode = \PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array())
 * @method string lastInsertId($name = null)
 * @method mixed errorCode()
 * @method array errorInfo()
 * @method mixed getAttribute($attribute)
 * @method string quote($string, $parameter_type = \PDO::PARAM_STR)
 *
 * phpcs:enable
 *
 */
interface ConnectionInterface
{
    /**
     * @return $this
     */
    public function beginTransaction();
}
