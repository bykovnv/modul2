<?php

namespace App;
use Aura\SqlQuery\QueryFactory;
use PDO;

class QueryBuilder {
private $pdo;
private $queryFactory;

function __construct(QueryFactory $qf, PDO $pdo)
{
    $this->pdo = $pdo;
    //$this->queryFactory = new QueryFactory('mysql');
    $this->queryFactory = $qf;
}

/**
 * Выводит все записи из таблицы
 * @param string table - название табллицы
 * @return array $result - все записи из таблицы
 */
public function getAll(string $table): array
{
    $select = $this->queryFactory->newSelect();
    $select->cols(['*'])->from($table);
    $sth = $this->pdo->prepare($select->getStatement());
    $sth->execute($select->getBindValues());
    $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

/**
 * Выводит одну строчку из таблицы по id
 * @param string table - название табллицы
 * @param int id - id записи
 * @return array result - одна строчка из таблицы
 */
public function getOne(string $table, int $id): array
{
    $select = $this->queryFactory->newSelect();
    $select->cols(['*'])->from($table)->where("id = $id");
    $sth = $this->pdo->prepare($select->getStatement());
    $sth->execute($select->getBindValues());
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    return $result;
}

/**
 * Вставить одну запись в таблицу
 * @param string table - название табллицы
 * @return array data получаем из $_POST
 * 
 */
public function insertOne(array $data, string $table): void
{
    $insert = $this->queryFactory->newInsert();
    $insert
    ->into($table)                   
    ->cols($data);
    $sth = $this->pdo->prepare($insert->getStatement());
    $sth->execute($insert->getBindValues());
}

/**
 * Добавление множества строк в таблицу
 * @param string table - название табллицы
 * @param array data получаем из $_POST
 * 
 */
public function insert(string $table, array $data): void {
    $insert = $this->queryFactory->newInsert();
    $insert->into($table);
    $insert->cols($data);
    $insert->addRow();
    $sth = $this->pdo->prepare($insert->getStatement());
    $sth->execute($insert->getBindValues());
}

/**
 * Удалить одну запись в таблицу по id
 * @param string table - название табллицы
 * @param int id пользователя или записи
 * 
 */
public function deleteOne(string $table, int $id): void {
    $delete = $this->queryFactory->newDelete();
    $delete
    ->from($table)                   // FROM this table
    ->where("id = $id");          // AND WHERE these conditions
    $sth = $this->pdo->prepare($delete->getStatement());
    $sth->execute($delete->getBindValues());
}

/**
 * Обновляет данные в одной выбранной записи по id
 * @param string table - название табллицы
 * @param array data получаем из $_POST
 * @param int id - записи, пользователя
 */
public function update(string $table, array $data, int $id): void
{
    $update = $this->queryFactory->newUpdate();
    $update->table($table)           
    ->cols($data)
    ->where("id = $id");
    $sth = $this->pdo->prepare($update->getStatement());
    $sth->execute($update->getBindValues());
}

}