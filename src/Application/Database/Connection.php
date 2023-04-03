<?php

namespace App\Application\Database;

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
    public function __construct($dsn = 'mysql:dbname=files_downloader;host=db', $username = 'root', $password = 'root')
    {
        try {
            $this->connection = new PDO($dsn, $username, $password);
        } catch(PDOException $e){
            echo $e->getMessage();
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