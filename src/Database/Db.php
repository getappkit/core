<?php

namespace Appkit\Database;

use Appkit\Core\App;
use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;

class Db
{
    /**
     * Get a connection instance from the global manager.
     *
     * @param string|null $connection
     * @return Connection
     */
    public static function connection(?string $connection = null): Connection
    {
        return App::instance()->getConnection($connection);
    }

    /**
     * Get a fluent query builder instance.
     *
     * @param  Closure|\Illuminate\Database\Query\Builder|string  $table
     * @param  string|null  $as
     * @param  string|null  $connection
     * @return \Illuminate\Database\Query\Builder
     */
    public static function table($table, $as = null, $connection = null)
    {
        return self::connection($connection)->table($table, $as);
    }

    /**
     * Get a schema builder instance.
     *
     * @param  string|null  $connection
     * @return Builder
     */
    public static function schema($connection = null)
    {
        return self::connection($connection)->getSchemaBuilder();
    }
}
