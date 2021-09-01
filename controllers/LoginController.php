<?php
require_once "controllers/DatabaseController.php";

class LoginController extends DatabaseController
{
    public function loginUser($email, $password){
        $hashPassword =  $this->getUserHashPassword($email);
        if ($hashPassword)
            return $this->checkPasswords($password,$hashPassword);
        else
            return -1;

    }
    private function getUserHashPassword($email){
        $statement = $this->mysqlDatabase->prepareStatement("SELECT ACCOUNT.password
                                                                    FROM ACCOUNT 
                                                                    INNER JOIN USER on ACCOUNT.user_id = USER.id
                                                                    WHERE USER.email = :email");
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchColumn();
    }
    private function checkPasswords($password,$hashPassword){
        return password_verify($password,$hashPassword);
    }

    public function setLoginTime($email,$type){
        $accountId = (int)$this->getTypeAccountId($email, $type);
        $statement = $this->mysqlDatabase->prepareStatement("INSERT INTO ACCESS (account_id, timestamp)
                                                                    VALUES (:accountId, :timestamp)");
        $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
        $statement->bindValue(':timestamp', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
        $statement->execute();
    }
    public function getTypeAccountId($email, $type){
        $statement = $this->mysqlDatabase->prepareStatement("SELECT ACCOUNT.id
                                                                    FROM ACCOUNT 
                                                                    INNER JOIN USER on ACCOUNT.user_id = USER.id
                                                                    WHERE USER.email = :email AND ACCOUNT.type= :type");
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->bindValue(':type', $type, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function getUserId($email){
        $statement = $this->mysqlDatabase->prepareStatement("SELECT USER.id
                                                                    FROM USER
                                                                    WHERE USER.email = :email");
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function hasPassword($userId){
        $statement = $this->mysqlDatabase->prepareStatement("SELECT ACCOUNT.password
                                                                    FROM ACCOUNT
                                                                    INNER JOIN USER ON ACCOUNT.user_id = USER.id
                                                                    WHERE USER.id = :id");
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function get2FaCode($email){
        $statement = $this->mysqlDatabase->prepareStatement("SELECT ACCOUNT.`2FA_code`
                                                                    FROM ACCOUNT
                                                                    INNER JOIN USER ON ACCOUNT.user_id = USER.id
                                                                    WHERE USER.email = :email");
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchColumn();
    }

}
