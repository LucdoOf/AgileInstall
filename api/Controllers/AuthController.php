<?php

namespace AgileAPI\Controllers;

use AgileAPI\AgileAPI;
use AgileCore\Models\Administrator;
use AgileCore\Models\User;

class AuthController extends Controller {

    protected $requireAuth = false;

    public function auth(){
        $name = AgileAPI::getInstance()->getPayload()["name"];
        $password = AgileAPI::getInstance()->getPayload()["password"];
        $admin = Administrator::select(["name" => $name]);
        if($admin->exist()){
            if(password_verify($password, $admin->password) || (isDev() && $password === $admin->password)){
                return $this->success("Connection réussie", $admin->toArray());
            } else {
                return $this->error401("Mot de passe incorrect");
            }
        } else {
            return $this->error401("Utilisateur inconnu");
        }
    }

}
