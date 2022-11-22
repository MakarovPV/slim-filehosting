<?php

namespace App\Application\Database;

use PDO;

class FileDataGateway
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->pdo = $connection->getConnection();
    }

    /**
     * Получение всех записей с пагинацией
     * @param int $skip
     * @param int $limit
     * @return array|false
     */
    public function getAllWithPaginate($skip, $limit)
    {
        $query = $this->pdo->prepare("SELECT * FROM files ORDER BY date DESC LIMIT $skip, $limit");
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Получение конкретного файла по имени
     * @param string $filename
     * @return mixed
     */
    public function getFileByName($filename)
    {
        $query = $this->pdo->prepare("SELECT * FROM files WHERE filename = $filename");
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Загрузка нового файла
     * @param string $filename
     * @param string $format
     * @param int $size
     * @return mixed
     */
    public function insert($filename, $format, $size)
    {
        $query = $this->pdo->prepare("INSERT INTO files (filename, format, size) VALUES (:filename, :format, :size)");
        $query->bindParam(':filename', $filename, PDO::PARAM_STR);
        $query->bindParam(':format', $format, PDO::PARAM_STR);
        $query->bindParam(':size', $size, PDO::PARAM_INT);
        $query->execute();

        $id = $this->pdo->lastInsertId();
        return $this->getFilenameById($id);
    }

    /**
     * Получение конкретного файла по id
     * @param int $id
     * @return mixed
     */
    private function getFilenameById($id)
    {
        $query = $this->pdo->prepare("SELECT `filename` FROM files WHERE id = $id");
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Получение количества записей в таблице
     * @return int
     */
    public function getCount()
    {
        $count = $this->pdo->prepare("SELECT * FROM files");
        $count->execute();
        return $count->rowCount();
    }


















}