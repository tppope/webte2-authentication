<?php
require_once "config.php";

class MysqlDatabase{
    private $connection;
    private array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
    ];

    /**
     * MysqlDatabase constructor.
     */
    public function __construct(){
        try {
            $this->setConnection(new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, $this->getOptions()));
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    /**
     * @return mixed
     */
    public function getConnection(){
        return $this->connection;
    }
    /**
     * @param mixed $connection
     */
    public function setConnection($connection){
        $this->connection = $connection;
    }
    /**
     * @return array
     */
    public function getOptions(): array{
        return $this->options;
    }
    /**
     * @param array $options
     */
    public function setOptions(array $options): void{
        $this->options = $options;
    }

    public function prepareStatement($query){
        return $this->getConnection()->prepare($query);
    }

}
