<?php
require_once "controllers/DatabaseController.php";

class RegistrationController extends DatabaseController
{

    public function performRegistration($name,$surname,$email,$type, $password, $googleId, $secret){
        try {
            $userId = $this->addUser($name,$surname,$email);
            $accountId = $this->addAccount($userId, $type, $password, $googleId,$secret);
            if ($type != 'own')
                $this->addAccess($accountId);
        }catch (PDOException $PDOException){
            throw $PDOException;
        }

    }
    public function addUser($name,$surname,$email){
        $statement = $this->mysqlDatabase->prepareStatement("INSERT INTO USER (name, surname, email)
                                                                    VALUES (:name, :surname, :email)");
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':surname', $surname, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        try {
            $statement->execute();
            return $this->mysqlDatabase->getConnection()->lastInsertId();
        }
        catch (PDOException $PDOException){
            throw $PDOException;
        }
    }

    public function addAccount($userId, $type, $password,$googleId,$secret){
        $statement = $this->mysqlDatabase->prepareStatement("INSERT INTO ACCOUNT (user_id, type, password, google_id, `2FA_code`)
                                                                    VALUES (:userId, :type, :password, :googleId, :secret)");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':type', $type, PDO::PARAM_STR);
        $statement->bindValue(':password', $password, PDO::PARAM_STR);
        $statement->bindValue(':googleId', $googleId, PDO::PARAM_STR);
        $statement->bindValue(':secret', $secret, PDO::PARAM_STR);
        try {
            $statement->execute();
            return $this->mysqlDatabase->getConnection()->lastInsertId();
        }
        catch (PDOException $PDOException){
            throw $PDOException;
        }
    }
    public function addAccess($accountId){
        $statement = $this->mysqlDatabase->prepareStatement("INSERT INTO ACCESS (account_id, timestamp)
                                                                    VALUES (:accountId, :timestamp)");
        $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
        $statement->bindValue(':timestamp', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
        try {
            $statement->execute();
        }
        catch (PDOException $PDOException){
            throw $PDOException;
        }
    }

}
