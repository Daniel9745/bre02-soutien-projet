<?php

class Router {
    public DefaultController $dc;

    public function handleRequest(? string $route) : void {
        $this->dc = new DefaultController();
        if($route === null)
        {
            $this->dc->homepage();
            // le code si il n'y a pas de route ( === la page d'accueil)
            // echo "je dois afficher la page d'accueil";
        }
        else
        {
            $this->dc->notFound();
            // le code si c'est aucun des cas précédents ( === page 404)
            // echo "je dois afficher la page 404";
        }
    }
}