<?php

namespace AgileCore\Models;

class Basket extends Model {

    public const STORAGE = "baskets";

    public const COLUMNS = [
      "id",
      "user_id",
      "ip"
    ];

    public const CONDITIONS = [
        "user_id" => [FILTER_VALIDATE_INT, ['min_range' => 1]],
        "ip" => "filterIp nullable"
    ];

    var $user_id = -1;
    var $ip = null;
    var $entries = [];
    var $user = null;

    /**
     * Retourne les entrées du panier
     *
     * @return BasketEntry[]
     */
    public function entries() {
        if(empty($this->entries)){
            $this->entries = BasketEntry::getAll(["basket_id" => $this->id]);
        }
        return $this->entries;
    }

    /**
     * Retourne l'utilisateur lié à la commande
     *
     * @return User
     */
    public function user() {
        if(is_null($this->user)){
            $this->user = new User($this->user_id);
        }
        return $this->user;
    }

    /**
     * @see Model
     */
    public function toArray() {
        $parentArray = parent::toArray();
        $parentArray["entries"] = [];
        foreach ($this->entries() as $entry){
            if(!is_null($entry)) $parentArray['entries'][] = $entry->toArray();
        }
        if(!is_null($this->user())) $parentArray["user"] = $this->user()->toArray();
        return $parentArray;
    }

    /**
     * @see Model
     */
    public function delete() {
        foreach ($this->entries() as $entry) {
            if ($entry->exist()) $entry->delete();
        }
        parent::delete();
    }

    /**
     * Récupère le montant total HT du panier
     *
     * @return float
     */
    public function getTotalHT() {
        return array_reduce($this->entries(), function ($total, $entry) {
            $total += $entry->getPriceHT();
            return $total;
        }, 0);
    }

    /**
     * Récupère le montant total TTC du panier
     *
     * @return float
     */
    public function getTotalTTC() {
        return array_reduce($this->entries(), function ($total, $entry) {
            $total += $entry->getPriceTTC();
            return $total;
        }, 0);
    }

}
