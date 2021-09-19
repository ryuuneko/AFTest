<?php

namespace RNW;

require_once('Psr/SimpleCache/CacheInterface.php');
require_once('InvalidArgumentException.php');

class CsvCache implements \Psr\SimpleCache\CacheInterface
{

    protected static $_cacheInstance;
    private $cache = [];
    private $defaultTTL = 60 * 60;

    private function __construct()
    {
        $fp = fopen('cache.csv', 'r');
        while ($row = fgetcsv($fp)) {
            $this->cache[$row[0]] = [
                'value' => $row[1],
                'expired' => $row[2]
            ];
        }
        fclose($fp);
    }

    public function updateCache()
    {
        $fp = fopen('cache.csv', 'w+');
        foreach ($this->cache as $key => $data) {
            $row = [$key, $data['value'], $data['expired']];
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    public function test()
    {
        var_dump($this->cache);
    }

    public static function getCache()
    {
        if (self::$_cacheInstance === null) {
            self::$_cacheInstance = new self;
        }

        return self::$_cacheInstance;
    }

    private function isIterable($item)
    {
        if (!is_iterable($item)) {
            throw new InvalidArgumentException($item, 'Iterable');
        }
    }

    private function isString($item)
    {
        if (!is_string($item)) {
            throw new InvalidArgumentException($item, 'String');
        }
    }

    public function get($key, $default = null)
    {
        $this->isString($key);

        $currentTime = time();
        return isset($this->cache[$key]) &&
            $this->cache[$key]['expired'] > $currentTime &&
            unserialize($this->cache[$key]['value']) ? $this->cache[$key]['value'] : $default;
    }

    public function set($key, $value, $ttl = null)
    {
        $this->isString($key);
        if ($ttl === null) {
            $ttl = $this->defaultTTL;
        }

        $currentTime = time();

        try {
            $this->cache[$key] = [
                'value' => serialize($value),
                'expired' => $currentTime + (int)$ttl
            ];
            $success = true;
        } catch (\Throwable $th) {
            $success = false;
        } finally {
            return $success;
        }
    }

    public function delete($key)
    {
        $this->isString($key);

        try {
            unset($this->cache[$key]);
            $success = true;
        } catch (\Throwable $th) {
            $success = false;
        } finally {
            return $success;
        }
    }

    public function clear()
    {
        try {
            $this->cache = [];
            $success = true;
        } catch (\Throwable $th) {
            $success = false;
        } finally {
            return $success;
        }
    }

    public function getMultiple($keys, $default = null)
    {
        $this->isIterable($keys);

        $result = [];
        foreach ($keys as $key) {
            $this->isString($key);
            $currentTime = time();
            $result[$key] = isset($this->cache[$key]) &&
                $this->cache[$key]['expired'] > $currentTime &&
                unserialize($this->cache[$key]['value']) ? $this->cache[$key]['value'] : $default;
        }

        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        $this->isIterable($values);
        if ($ttl === null) {
            $ttl = $this->defaultTTL;
        }
        $currentTime = time();
        foreach ($values as $key => $value) {
            $this->isString($key);

            try {
                $this->cache[$key] = [
                    'value' => serialize($value),
                    'expired' => $currentTime + (int)$ttl
                ];
                $success = true;
            } catch (\Throwable $th) {
                $success = false;
                break;
            }
        }
        return $success;
    }

    public function deleteMultiple($keys)
    {
        $this->isString($keys);

        foreach ($keys as $key) {
            $this->isString($key);
            try {
                unset($this->cache[$key]);
                $success = true;
            } catch (\Throwable $th) {
                $success = false;
                break;
            }
        }
        return $success;
    }

    public function has($key)
    {
        $this->isString($key);
        return isset($this->cache[$key]) ? true : false;
    }
}
