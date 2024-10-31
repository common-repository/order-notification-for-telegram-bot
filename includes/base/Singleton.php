<?php

namespace OrderNotificationForTelegramBot\Base;

use Exception;

abstract class Singleton
{
    private static array $instances = [];

    protected function __construct()
    {
        $this->init();
    }

    protected function __clone()
    {
    }


    /**
     * @throws Exception
     */

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): Singleton
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    abstract function init();
}
