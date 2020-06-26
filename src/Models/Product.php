<?php

namespace AgileCore\Models;

class Product extends Model {

    use Referenceable;

    public const STORAGE = "products";
    public const REFERENCE_PREFIX = "PRD";

    public const COLUMNS = [
        "id",
        "reference",
        "name",
        "description",
        "stock",
        "price",
        "boosted",
        "enabled"
    ];

    public const CONDITIONS = [
        "stock" => [FILTER_VALIDATE_INT, ['min_range' => 0]],
        "price" => 'filterStrictPositiveFloat',
        "boosted" => FILTER_VALIDATE_BOOLEAN,
        "enabled" => FILTER_VALIDATE_BOOLEAN
    ];

    var $name = "";
    var $description = "";
    var $stock = -1;
    var $price = -1;
    var $boosted = false;
    var $enabled = false;

}
