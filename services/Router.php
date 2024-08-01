<?php

class Router
{
    public DefaultController $dc;
    public AuthController $ac;
    public AdminController $adc;
    public UserController $uc;

    public function handleRequest(?string $route): void
    {
        $this->dc = new DefaultController();
        $this->ac = new AuthController();
        $this->adc = new AdminController();
        $this->uc = new UserController();

        if ($route === null) {
            $this->dc->homepage();
        } 
        else if ($route === "admin") 
        {
            // $this->checkAdminAccess();
            $this->adc->home();
        } 
        else if ($route === "admin-connexion") 
        {
            // $this->checkAdminAccess();
            $this->adc->login();
        }
        else if ($route === "admin-check-connexion") 
         {
            // $this->checkAdminAccess();
            $this->adc->checkLogin();
        } 
        else if ($route === "admin-create-user")
        {
            // $this->checkAdminAccess();
            $this->uc->create();
        }
        else if ($route === "admin-edit-user") 
        {
            // $this->checkAdminAccess();
            $this->uc->edit();
        } 
        else if ($route == "admin-chek-edit-user") 
        {
            // $this->checkAdminAccess();
            $this->uc->checkEdit();
        } 
        else if ($route === "admin-delete-user") 
        {
            // $this->checkAdminAccess();
            $this->uc->delete();
        } 
        else if ($route === "admin-list-users") 
        {
            // $this->checkAdminAccess();
            $this->uc->list();
        } 
        else if ($route === "admin-show-user") 
        {
            // $this->checkAdminAccess();
            $this->uc->show();
        } 
        else if ($route === "inscription") 
        {
            $this->ac->register();
        } 
        else if ($route === "check-inscription") 
        {
            $this->ac->checkRegister();
        } 
        else if ($route === "connexion") 
        {
            $this->ac->login();
        } 
        else if ($route === "check-connexion") 
        {
            $this->ac->checkLogin();
        } 
        else if ($route === "deconnexion") 
        {
            $this->ac->logout();
        } 
        else 
        {
            $this->dc->notFound();
        }
    }

    private function checkAdminAccess(): void
    {
        if (
            isset($_SESSION['user'])
            && isset($_SESSION['role']) && $_SESSION['role'] === "ADMIN"
        ) {
            // c'est bon
            $this->adc->home();
        } else {
            // c'est pas bon : redirection avec un header('Location:')
            $this->redirect("admin-connexion");
        }
    }
    protected function redirect(?string $route): void
    {
        if ($route !== null) {
            header("Location: index.php?route=$route");
        } else {
            header("Location: index.php");
        }
        exit();
    }
}
