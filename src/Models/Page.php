<?php

namespace AgileCore\Models;

class Page extends Model {

    public const STORAGE = "pages";
    public const NAME = 'page';

    public const COLUMNS = [
        "id",
        "slug",
        "name",
        "static"
    ];

    public const CONDITIONS = [
        "slug" => "filterString",
        "name" => "filterString",
        "static" => FILTER_VALIDATE_BOOLEAN
    ];

    var $slug = "";
    var $name = "";
    var $static = true;

}
