<?php

namespace AgileCore\Models;

class Administrator extends Model {

    public const STORAGE = "administrators";

    public const COLUMNS = [
        "id",
        "name",
        "password"
    ];

    var $name = "";
    var $password = "";

}
