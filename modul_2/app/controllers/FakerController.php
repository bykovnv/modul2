<?php
namespace App\controllers;
use PDO;
use Aura\SqlQuery\QueryFactory;
use App\QueryBuilder;
use Faker;

class FakerController {

    private $templates;
    private $db;
    private $queryFactory;
    private $pdo;
    
    function __construct()
    {
        $this->db = new QueryBuilder(); 
        $this->queryFactory = new QueryFactory('mysql');
        $this->pdo = new PDO("mysql:host=localhost;dbname=alla_doodee_r_db; charset=utf8;", "alla_doodee__usr", "zij0ylH0574WY1aK" );
     }
     
    public function faker($vars){
        $faker = Faker\Factory::create();
        $insert = $this->queryFactory->newInsert();
        $insert->into('posts');
        for ($i = 0; $i < 30; $i++ ) {
            $insert->cols([
                "title" => $faker->words(3, true),
                "text" => $faker->text, 
            ]);
            $insert->addRow();
        }
        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues()); 
    } 
}
 