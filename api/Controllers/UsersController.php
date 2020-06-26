<?php

namespace AgileAPI\Controllers;

use AgileCore\Models\Model;
use AgileCore\Models\User;

class UsersController extends Controller {

    public function getUsersPage($page){
        return Model::listToArray(User::page($page-1, $this->getSortKey() ?? "inscription_date DESC", $this->getFilters()));
    }

    public function getUsers(){
        return Model::listToArray(User::getAll());
    }

}
