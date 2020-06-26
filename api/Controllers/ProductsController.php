<?php

namespace AgileAPI\Controllers;

use AgileAPI\AgileAPI;
use AgileCore\Database\SQL;
use AgileCore\Models\Basket;
use AgileCore\Models\BasketEntry;
use AgileCore\Models\Command;
use AgileCore\Models\Model;
use AgileCore\Models\Product;
use AgileCore\Utils\Dbg;
use AgileCore\Utils\Plural;

class ProductsController extends Controller {

    public function getProducts(){
        return Model::listToArray(Product::getAll());
    }

    public function updateProduct($id){
        $product = new Product($id);
        if($product->exist()){
            $product->hydrate(AgileAPI::getInstance()->getPayload());
            $valid = $product->isValid();
            if($valid === true){
                $product->save();
                return $this->message("Produit sauvegardé");
            } else {
                return $this->error400("Champ " . $valid . " invalide");
            }
        } else {
            return $this->error404("Produit introuvable");
        }
    }

    public function createProduct(){
        $product = new Product();
        $product->hydrate(AgileAPI::getInstance()->getPayload());
        $sameName = Product::select(["name" => $product->name]);
        $valid = $product->isValid();
        if($valid === true) {
            if (!$sameName->exist()) {
                $product->save();
                return $this->message("Produit " . $product->name . " créé");
            } else {
                return $this->error400("Un produit du même nom existe déjà");
            }
        } else {
            return $this->error400("Champ " . $valid . " invalide");
        }
    }

    public function getProductLinkedCommands($id, $page){
        $product = new Product($id);
        if($product->exist()){
            return Model::listToArray(Command::page(
                $page-1,
                $this->getSortKey() ?? "order_date DESC",
                array_merge($this->getFilters(), [Plural::singularize(BasketEntry::STORAGE) . ".product_id" => $id])
            ));
        } else {
            return $this->error404("Produit introuvable");
        }
    }



}
