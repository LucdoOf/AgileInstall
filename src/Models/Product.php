<?php

namespace AgileCore\Models;

class Product extends Model {

    use Referenceable;

    public const STORAGE = "products";
    public const REFERENCE_PREFIX = "PRD";
    public const NAME = 'product';

    public const COLUMNS = [
        "id",
        "reference",
        "name",
        "category_id",
        "description",
        "stock",
        "price",
        "boosted",
        "enabled"
    ];

    public const CONDITIONS = [
        "name" => "filterString",
        "stock" => [FILTER_VALIDATE_INT, ['min_range' => 0]],
        "category_id" => [FILTER_VALIDATE_INT, ['min_range' => 1]],
        "price" => 'filterStrictPositiveFloat',
        "boosted" => FILTER_VALIDATE_BOOLEAN,
        "enabled" => FILTER_VALIDATE_BOOLEAN
    ];

    var $name = "";
    var $description = "";
    var $stock = -1;
    var $price = -1;
    var $category_id = -1;
    var $category = null;
    var $boosted = false;
    var $enabled = false;

    /**
     * Retourne la catégorie associée au produit
     *
     * @return Category
     */
    public function category() {
        if(is_null($this->category)){
            $this->category = new Category($this->category_id);
        }
        return $this->category;
    }

    /**
     * Retourne la liste des médias associés
     *
     * @return ProductMedia[]
     */
    public function getLinkedMedias() {
        return ProductMedia::getAll(['product_id' => $this->id]);
    }

    /**
     * @inheritDoc
     */
    public function toArray($excludedKeys = []) {
        $parentArray = parent::toArray($excludedKeys);
        if($this->category()->exist()) $parentArray["category"] = $this->category()->toArray();
        $parentArray['linked_medias'] = $this->getLinkedMedias();
        return $parentArray;
    }

}
