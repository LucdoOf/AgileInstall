<?php

namespace AgileCore\Models;

class Administrator extends Model {

    public const STORAGE = "administrators";
    public const NAME = "administrator";

    public const COLUMNS = [
        "id",
        "name",
        "password",
        "last_seen"
    ];

    var $name = "";
    var $password = "";
    var $last_seen = null;

}
