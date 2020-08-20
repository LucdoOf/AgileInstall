<?php

namespace AgileCore\Models;

class Category extends Model {

    public const STORAGE = 'categories';
    public const NAME = 'category';

    const COLUMNS = [
        "id",
        "name",
        "slug",
        "parent_id",
        "created_at"
    ];

    const CONDITIONS = [
        "name" => "filterString",
        "slug" => "filterString",
        "parent_id" => "filterStrictPositiveInt nullable",
        "created_at" => "filterDate"
    ];

    var $name = "";
    var $slug = "";
    var $parent_id = null;
    var $created_at = null;
    var $parent = null;

    /**
     * Retourne la catégorie parente
     *
     * @return Category
     */
    public function parent() {
        if(is_null($this->parent)){
            $this->parent = new Category($this->parent_id);
        }
        return $this->parent;
    }

    /**
     * Retourne le parent le plus haut ou la catégorie si pas de parent
     *
     * @return Category
     */
    public function root() {
        if($this->parent()->exist()){
            return $this->parent()->root()->exist() ? $this->parent()->root() : $this;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray($excludedKeys = []) {
        $parentArray = parent::toArray($excludedKeys);
        if($this->parent()->exist()) $parentArray["parent"] = $this->parent()->toArray();
        $parentArray["root"] = $this->root();
        return $parentArray;
    }

    /**
     * @param null $key
     * @param array $excludedKeys
     * @return bool|int|string
     * @see Model
     *
     */
    public function isValid($key = null, $excludedKeys = []) {
        if(is_null($key) || $key !== 'parent_id') {
            return parent::isValid($key);
        } else {
            $parent = $this->parent();
            if($parent->exist()){
                if($parent->parent_id === $this->id) return $key;
            }
            return true;
        }
    }

}
