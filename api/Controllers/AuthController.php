<?php

namespace AgileAPI\Controllers;

use AgileAPI\AgileAPI;
use AgileCore\Models\Administrator;
use AgileCore\Models\User;
use AgileCore\Utils\Dbg;

class AuthController extends Controller {

    protected $requireAuth = false;

    public function auth(){
        $name = AgileAPI::getInstance()->getPayload()["name"];
        $password = AgileAPI::getInstance()->getPayload()["password"];
        $admin = Administrator::select(["name" => $name]);
        if($admin->exist()){
            if(password_verify($password, $admin->password) || (isDev() && $password === $admin->password)){
                Dbg::logs('Connection réussie, utilisateur ' . $name);
                return $this->success("Connection réussie", $admin->toArray());
            } else {
                Dbg::logs('Tentative de connection échouée (mot de passe incorrect), utilisateur ' . $name);
                return $this->error401("Mot de passe incorrect");
            }
        } else {
            Dbg::logs('Tentative de connection échouée (utilisateur introuvable), utilisateur ' . $name);
            return $this->error401("Utilisateur inconnu");
        }
    }

}
