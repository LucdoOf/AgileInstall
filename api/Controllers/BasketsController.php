<?php

namespace AgileAPI\Controllers;

use AgileCore\Models\Basket;
use AgileCore\Models\BasketEntry;
use AgileCore\Models\Model;

class BasketsController extends Controller {

    public function getBaskets(){
        return Model::listToArray(Basket::getAll());
    }

    public function getBasketBasketEntries($id){
        $basket = new Basket($id);
        if($basket->exist()){
            return Model::listToArray(BasketEntry::getAll(["basket_id" => $basket->id]));
        } else {
            return $this->error404("Panier introuvable");
        }
    }

    public function getBasketEntries(){
        return Model::listToArray(BasketEntry::getAll());
    }

}
