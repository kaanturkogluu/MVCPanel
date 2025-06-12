<?php

class Database
{
    private static $instances = [];
    private $connection;

    private function __construct($config=null)
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $this->connection = new PDO($dsn, $config['username'], $config['password'], $options);
    }

    public static function getInstance(string $dbKey = 'default'): self
    {
        if (!isset($GLOBALS['db_config'][$dbKey])) {
            throw new Exception("Veritabanı yapılandırması bulunamadı: $dbKey");
        }

        if (!isset(self::$instances[$dbKey])) {
            self::$instances[$dbKey] = new self($GLOBALS['db_config'][$dbKey]);
        }

        return self::$instances[$dbKey];
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
