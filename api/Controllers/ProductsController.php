<?php

namespace AgileAPI\Controllers;

use AgileAPI\AgileAPI;
use AgileCore\Database\SQL;
use AgileCore\Models\Basket;
use AgileCore\Models\BasketEntry;
use AgileCore\Models\Category;
use AgileCore\Models\Command;
use AgileCore\Models\Model;
use AgileCore\Models\Product;
use AgileCore\Models\ProductMedia;
use AgileCore\Utils\Dbg;
use AgileCore\Utils\Plural;
use AgileCore\Utils\Str;
use DateTime;

class ProductsController extends Controller {

    public function getProduct($id){
        $product = new Product($id);
        if($product->exist()) {
            return $product->toArray();
        } else {
            return $this->error404("Produit introuvable");
        }
    }

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

    public function getCategories() {
        return Model::listToArray(Category::getAll());
    }

    public function createCategory(){
        $category = new Category();
        $category->hydrate($this->payload());
        $category->slug = Str::slugify($category->name);
        $category->created_at = new DateTime();
        $sameName = Category::select(["name" => $category->name]);
        $valid = $category->isValid();
        if($valid === true) {
            if (!$sameName->exist()) {
                $category->save();
                return $this->message("Catégorie " . $category->name . " créé");
            } else {
                return $this->error400("Une catégorie du même nom existe déjà");
            }
        } else {
            return $this->error400("Champ " . $valid . " invalide");
        }
    }

    public function updateCategory($id){
        $category = new Category($id);
        if($category->exist()){
            $category->hydrate($this->payload());
            $category->slug = Str::slugify($category->name);
            $valid = $category->isValid();
            if($valid === true){
                $category->save();
                return $this->message("Catégorie sauvegardée");
            } else {
                return $this->error400("Champ " . $valid . " invalide");
            }
        } else {
            return $this->error404("Catégorie introuvable");
        }
    }

    public function uploadMedia($id){
        $product = new Product($id);
        if($product->exist()){
            if(!empty($_FILES)){
                $file = array_shift($_FILES);
                $productMedia = new ProductMedia();
                $productMedia->hydrate($this->payload());
                $productMedia->product_id = $product->id;
                $productMedia->mime = $file['type'];
                $productMedia->updateReference();
                $valid = $productMedia->isValid(null, ['url']);
                if($valid === true) {
                    $tmpName = $file['tmp_name'];
                    if(move_uploaded_file($tmpName, INSTALL_ROOT . '/public/uploads/products/medias/' . $productMedia->reference . '.' . mime2ext($productMedia->mime))){
                        $productMedia->url = public_url() . '/uploads/products/medias/' . $productMedia->reference . '.' . mime2ext($productMedia->mime);
                        $valid = $productMedia->isValid();
                        if($valid === true) {
                            $productMedia->save();
                            return $this->message("Média téléchargé");
                        } else {
                            return $this->error400('Champ ' . $valid . ' invalide (2)');
                        }
                    } else {
                        return $this->error400('Une erreur est survenue lors du téléchargement du fichier');
                    }
                } else {
                    return $this->error400('Champ ' . $valid . ' invalide');
                }
            } else {
                return $this->error400("Veuillez renseigner un fichier");
            }
        } else {
            return $this->error404("Produit introuvable");
        }
    }

}
