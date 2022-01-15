<?php

class UsersRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function add($username, $password)
    {
        $password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
    }

    public function getPassword($username)
    {
        $sql = "SELECT password FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['password'] ?? null;
    }

    public function exists($username)
    {
        $password = $this->getPassword($username);
        return $password ? true : false;
    }

    public function changePassword($username, $password)
    {
        $oldPass = $this->getPassword($username);
        $password = password_hash($password, PASSWORD_BCRYPT);
        if ($oldPass != null) {
            $sql = "UPDATE users SET password=:password WHERE username=:username";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
        }
    }
}
