<?php

namespace AgileAPI\Controllers;

use AgileCore\Models\Command;
use AgileCore\Models\Model;
use AgileCore\Models\User;

class UsersController extends Controller {

    public function getUser($id){
        $user = new User($id);
        if($user->exist()) {
            return $user->toArray();
        } else {
            return $this->error404("Utilisateur introuvable");
        }
    }

    public function getUserCommands($id){
        $user = new User($id);
        if($user->exist()) {
            return Model::listToArray(Command::getAll(["basket.user_id" => $id], null, null, null, Command::getJoinStr()));
        } else {
            return $this->error404("Utilisateur introuvable");
        }
    }

    public function getUsersPage($page){
        return Model::listToArray(User::page($page-1, $this->getSortKey() ?? "inscription_date DESC", $this->getFilters()));
    }

    public function getUsers(){
        return Model::listToArray(User::getAll());
    }

}
