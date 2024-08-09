<?php

class UserController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create(): void
    {
        if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["role"])) {
            $email = $_POST["email"];
            $password = $_POST["password"];
            $role = $_POST["role"];
            $user = new User($email, $password, $role);
            $userManager = new UserManager();
            $creatUser = $userManager->createUser($user);
        }
        $this->render("admin/users/create.html.twig", []);
    }

    public function checkCreate(): void
    {

        if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"]) && isset($_POST["role"])) {
            $tokenManager = new CSRFTokenManager();

            //Vérifie si le csrf_token est présent et utilise le CSRFTokenManager pour vérifier que le token reçu est le bon, 

            if (isset($_POST["csrf_token"]) && $tokenManager->validateCSRFToken($_POST["csrf_token"])) {
                if ($_POST["password"] === $_POST["confirm_password"]) {

                    $password_pattern = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/";

                    if (preg_match($password_pattern, $_POST["password"])) {
                        $um = new UserManager();
                        $user = $um->findUserByEmail($_POST["email"]);

                        if ($user === null) {
                            $email = htmlspecialchars($_POST["email"]);
                            $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
                            $role = htmlspecialchars($_POST["role"]);
                            $user = new User($email, $password, $role);

                            $um->createUser($user);

                            $_SESSION["user"] = $user->getId();

                            unset($_SESSION["error_message"]);

                            $this->redirect("admin-list-user");
                        } else {
                            $_SESSION["error_message"] = "User already exists";
                            $this->redirect("admin-create-user");
                        }
                    } else {
                        $_SESSION["error_message"] = "Password is not strong enough";
                        $this->redirect("admin-create-user");
                    }
                } else {
                    $_SESSION["error_message"] = "The passwords do not match";
                    $this->redirect("admin-create-user");
                }
            } else {
                $_SESSION["error_message"] = "Invalid CSRF token";
                $this->redirect("admin-create-user");
            }
        } else {
            $_SESSION["error_message"] = "Missing fields";
            $this->redirect("admin-create-user");
        }
    }

    public function edit(): void
    {
        if (isset($_GET['user_id'])) {
            $userId = intval($_GET['user_id']);
            $um = new UserManager();
            $user = $um->findUserById($userId);

            if ($user) 
            {
                $this->render("admin/users/edit.html.twig", ["user" => $user]);
            } 
            else 
            {
                $this->redirect("admin-list-users");
            }
        } 
        else 
        {
            $this->redirect("admin-list-users");
        }
    }

    public function checkEdit(): void
    {
         {
            if (isset($_POST["user_id"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"]) && isset($_POST["role"])) {
                $tokenManager = new CSRFTokenManager();

                if (isset($_POST["csrf_token"]) && $tokenManager->validateCSRFToken($_POST["csrf_token"])) {
                    if ($_POST["password"] === $_POST["confirm_password"]) {
                        $password_pattern = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/";

                        if (preg_match($password_pattern, $_POST["password"])) {
                            $um = new UserManager();
                            $user = $um->findUserById(intval($_POST["user_id"]));

                            if ($user) {
                                $email = htmlspecialchars($_POST["email"]);
                                $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
                                $role = htmlspecialchars($_POST["role"]);

                                $user->setEmail($email);
                                $user->setPassword($password);
                                $user->setRole($role);

                                $um->updateUser($user);

                                unset($_SESSION["error_message"]);
                                $this->redirect("admin-list-users");
                            } else {
                                $_SESSION["error_message"] = "User not found";
                                $this->redirect("admin-edit-user&user_id=" . intval($_POST["user_id"]));
                            }
                        } else {
                            $_SESSION["error_message"] = "Password is not strong enough";
                            $this->redirect("admin-edit-user&user_id=" . intval($_POST["user_id"]));
                        }
                    } else {
                        $_SESSION["error_message"] = "The passwords do not match";
                        $this->redirect("admin-edit-user&user_id=" . intval($_POST["user_id"]));
                    }
                } else {
                    $_SESSION["error_message"] = "Invalid CSRF token";
                    $this->redirect("admin-edit-user&user_id=" . intval($_POST["user_id"]));
                }
            } else {
                $_SESSION["error_message"] = "Missing fields";
                $this->redirect("admin-edit-user&user_id=" . intval($_POST["user_id"]));
            }
        }
    }

    public function delete(): void
    {
        if (isset($_GET['user_id'])) {
            $userId = intval($_GET['user_id']);
            $um = new UserManager();
            $um->deleteUser($userId);
        }
    
        $this->redirect("admin-list-users");
    }

    public function list(): void
    {
        $user = new UserManager();
        $users = $user->findAllUsers();
        $this->render("admin/users/list.html.twig", ["users" => $users]);
    }

    public function show(int $id): void
    {
        $um = new UserManager();
        $user = $um->findUserById($id);
        if ($user) {
            $this->render("admin/users/show.html.twig", ["user" => $user]);
        } else {
            $this->redirect("admin-list-users");
        }
    }
}
