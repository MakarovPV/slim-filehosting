<?php

namespace App\Application\Database;

use http\Exception\InvalidArgumentException;
use PDO;
use PDOException;

class Connection
{
    /**
     * @var PDO
     */
    private PDO $connection;

    /**
     * Подключение к БД
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
    public function __construct($dsn = 'mysql:dbname=files_downloader;host=127.0.0.1', $username = 'root', $password = 'root')
    {
        try {
            $this->connection = new PDO($dsn, $username, $password);
        } catch(PDOException $e){
            throw new InvalidArgumentException('error');
        }

    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
}