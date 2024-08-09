<?php

class AuthController extends AbstractController
{

    private UserManager $um;

    public function __construct()
    {
        parent::__construct();
        $this->um = new UserManager();
    }

    public function register(): void
    {
        $this->render('front/register.html.twig', []);
    }

    public function checkRegister(): void
    {
        //vérifie que tous les champs du formulaire (email, password, confirm_password) sont bien présents. 
        //Si ce n'est pas le cas elle redirige vers la page d'inscription et affiche un message d'erreur.
        if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {
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
                            $user = new User($email, $password, 'USER');

                            $um->createUser($user);

                            $_SESSION["user"] = $user->getId();

                            unset($_SESSION["error_message"]);

                            $this->redirect("connexion");
                        } else {
                            $_SESSION["error_message"] = "User already exists";
                            $this->redirect("inscription");
                        }
                    } else {
                        $_SESSION["error_message"] = "Password is not strong enough";
                        $this->redirect("inscription");
                    }
                } else {
                    $_SESSION["error_message"] = "The passwords do not match";
                    $this->redirect("inscription");
                }
            } else {
                $_SESSION["error_message"] = "Invalid CSRF token";
                $this->redirect("inscription");
            }
        } else {
            $_SESSION["error_message"] = "Missing fields";
            $this->redirect("inscription");
        }
    }
    public function login(): void
    {
        $this->render('front/login.html.twig', []);
    }

    public function logout(): void
    {
        session_start();
        session_destroy();
    }
}
