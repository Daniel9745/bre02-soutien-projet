<?php

class UserManager extends AbstractManager
{

    public function __construct()
    {
        // J'appelle le constructeur de l'AbstractManager pour qu'il initialise la connexion à la DB
        parent::__construct();
    }
    public function createUser(User $user): User
    {
        $query = $this->db->prepare('INSERT INTO users (id, email, password, role) VALUES (NULL, :email, :password, :role)');
        $parameters = [
            "email" => $user->getEmail(),
            "password" => $user->getPassword(),
            "role" => $user->getRole()
        ];
        $query->execute($parameters);
        $user->setId($this->db->lastInsertId());

        return $user;
        dump($user);
    }

    public function findUserByEmail(string $email): ?User
    {
        $query = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $parameters =
            [
                "email" => $email
            ];
        $query->execute($parameters);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user !== null) {
            $users = new User($user["email"], $user["password"], $user["role"]);
            $users->setId($user['id']);
        }
        return $users;
    }

    public function findAllUsers(): array
    {
        $users = [];
        $query= $this->db->prepare('SELECT * FROM users');
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach($result as $item){
            $user = new User($item["email"], $item["password"],$item["role"]);
            $user->setId($item["id"]);

            $users[] = $user;
        }
        return $users;
    }
}
