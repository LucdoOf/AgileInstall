<?php

namespace AgileAPI\Controllers;

use AgileCore\Models\Command;
use AgileCore\Models\Model;
use AgileCore\Models\Product;
use AgileCore\Models\User;
use AgileCore\Utils\Dbg;
use AgileCore\Utils\Str;

class AdminController extends Controller {

    public function search($search_key) {
        $searchableClasses = [Command::class, Product::class, User::class];
        // Si la requête commence par une référence reconnue on cherche l'objet
        foreach ($searchableClasses as $searchableClass) {
            /** @var $searchableClass Command|Product|User */
            if(Str::startsWith($search_key, $searchableClass::REFERENCE_PREFIX)) {
                return Model::listToArray($searchableClass::search($search_key));
            }
        }
        // Sinon on cherche par défaut un utilisateur ou un produit
        return Model::listToArray(array_merge(User::search($search_key), Product::search($search_key)));
    }

}
