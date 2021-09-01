<?php
require_once "controllers/DatabaseController.php";
require_once "models/User.php";


class AccountController extends DatabaseController
{
    public function getUser(): ?User{
        session_start();
        if (isset($_SESSION["user_id"])) {
            $userId = $_SESSION["user_id"];
            $statement = $this->mysqlDatabase->prepareStatement("SELECT USER.id AS id, USER.name AS name, USER.surname AS surname, USER.email AS email
                                                        FROM USER
                                                        WHERE USER.id = :id");
            $statement->bindValue(':id', $userId, PDO::PARAM_INT);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS, "User");
            return $statement->fetch();
        }
        else{
            return null;
        }
    }

    public function getLoginType($userId): string{
        $statement = $this->mysqlDatabase->prepareStatement("SELECT ACCOUNT.type
                                                                    FROM ACCOUNT
                                                                    INNER JOIN USER ON ACCOUNT.user_id = USER.id
                                                                    WHERE USER.id = :id");
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function getHistoryList(): array{
        session_start();
        $userId = $_SESSION["user_id"];
        $statement = $this->mysqlDatabase->prepareStatement("SELECT ACCESS.timestamp
                                                                    FROM ACCESS
                                                                    INNER JOIN ACCOUNT ON ACCESS.account_id = ACCOUNT.id
                                                                    INNER JOIN USER ON ACCOUNT.user_id = USER.id
                                                                    WHERE USER.id = :id
                                                                    ORDER BY ACCESS.timestamp");
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        return $statement->fetchAll();
    }
    public function getLoginStats(): array{
        session_start();
        $userId = $_SESSION["user_id"];
        $statement = $this->mysqlDatabase->prepareStatement("SELECT ACCOUNT.type, COUNT(*) AS typeCount
                                                                    FROM ACCOUNT
                                                                    INNER JOIN ACCESS on ACCOUNT.id = ACCESS.account_id
                                                                    GROUP BY ACCOUNT.type
                                                                    ORDER BY ACCOUNT.type");
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        return $statement->fetchAll();
    }

}
