<?php

namespace AgileCore\Models;

class BasketEntry extends Model {

    public const STORAGE = "basket_entries";
    public const NAME = "basket_entry";

    public const COLUMNS = [
        "id",
        "basket_id",
        "product_id",
        "quantity",
        "entry_price",
        "entry_discount"
    ];

    public const CONDITIONS = [
        "basket_id" => [FILTER_VALIDATE_INT, ['min_range' => 1]],
        "product_id" => [FILTER_VALIDATE_INT, ['min_range' => 1]],
        'quantity' => [FILTER_VALIDATE_INT, ['min_range' => 1]],
        "entry_price" => 'filterStrictPositiveFloat nullable', // Null jusqu'à validation de la commande pour éviter les problèmes de fin de réductions par ex
        "entry_discount" => 'filterPositiveFloat nullable' // Null jusqu'à validation de la commande -------------------------- // ---------------------------
    ];

    var $basket_id = -1;
    var $product_id = -1;
    var $quantity = -1;
    var $entry_price = null;
    var $entry_discount = null;
    var $product = null;

    /**
     * Retourne le produit associé à l'entrée
     *
     * @return Product
     */
    public function product() {
        if(is_null($this->product)) {
            $this->product = new Product($this->product_id);
        }
        return $this->product;
    }

    /**
     * Retourne le prix hors TVA de l'entrée
     *
     * @return float|int
     */
    public function getPriceHT() {
        return $this->entry_price / (1 + 0.20);
    }

    /**
     * Retourne le prix hors TVA unitaire de l'entrée
     *
     * @return float|int
     */
    public function getUnitPriceHT() {
        return $this->getPriceHT() / $this->quantity;
    }

    /**
     * Retourne le prix TTC de l'entrée
     *
     * @return null|float
     */
    public function getPriceTTC() {
        return $this->entry_price;
    }

    /**
     * Retourne le prix unitaire TTC de l'entrée
     *
     * @return float|int
     */
    public function getUnitPriceTTC() {
        return $this->getPriceTTC() / $this->quantity;
    }

    /**
     * Indique si l'entrée a subit une réduction
     *
     * @return bool
     */
    public function hasDiscount() {
        return !is_null($this->entry_discount) && (float)$this->entry_discount > 0;
    }

    /**
     * @inheritDoc
     */
    public function toArray($excludedKeys = []) {
        $parentArray = parent::toArray($excludedKeys);
        if ($this->product()->exist()) $parentArray["product"] = $this->product()->toArray();
        return $parentArray;
    }

}
