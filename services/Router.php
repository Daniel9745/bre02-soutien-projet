<?php

class Router
{
    private DefaultController $dc;
    private AuthController $ac;
    private AdminController $adc;
    private UserController $uc;

    public function __construct()
    {
        $this->dc = new DefaultController();
        $this->ac = new AuthController();
        $this->adc = new AdminController();
        $this->uc = new UserController();
    }

    public function handleRequest(?string $route): void
    {


        if ($route === null)
        {
            $this->dc->homepage();
        } 
        else if ($route === "admin") 
        {
            $this->checkAdminAccess();
            $this->adc->home();
        } 
        else if ($route === "admin-connexion") 
        {
            $this->adc->login();
        } 
        else if ($route === "admin-check-connexion") 
        {
            $this->adc->checkLogin();
        } 
        else if ($route === "admin-create-user") 
        {
            $this->checkAdminAccess();
            $this->uc->create();
        } 
        else if ($route === "admin-check-create-user") 
        {
            $this->checkAdminAccess();
            $this->uc->checkCreate();
        } 
        else if ($route === "admin-edit-user") 
        {
            $this->checkAdminAccess();
            if (isset($_GET['user_id'])) 
            {
                $userId = intval($_GET['user_id']);
                $this->uc->edit($userId);
            } 
            else 
            {
                $this->redirect("admin-list-users");
            }
        } 
        else if ($route == "admin-chek-edit-user") 
        {
            $this->checkAdminAccess();
            $this->uc->checkEdit();
        } 
        else if ($route === "admin-delete-user") 
        {
            $this->checkAdminAccess();
            if (isset($_GET['user_id'])) 
            {
                $userId = intval($_GET['user_id']);
                $this->uc->delete($userId);
            } 
            else 
            {
                $this->redirect("admin-list-users");
            }
        } 
        else if ($route === "admin-list-users") 
        {
            $this->checkAdminAccess();
            $this->uc->list();
        } 
        else if ($route === "admin-show-user") 
        {
            $this->checkAdminAccess();
            if (isset($_GET['user_id'])) 
            {
                $userId = intval($_GET['user_id']);
                $this->uc->show($userId);
            }
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
        if (isset($_SESSION['user']) && isset($_SESSION['role']) && $_SESSION['role'] === "ADMIN") 
        {
            $this->adc->home();
        } 
        else 
        {

            $this->redirect("admin-connexion");
        }
    }
    protected function redirect(?string $route): void
    {
        if ($route !== null) {
            header("Location: index.php?route=$route");
        } 
        else 
        {
            header("Location: index.php");
        }
        exit();
    }
}
