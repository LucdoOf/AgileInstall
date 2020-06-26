<?php

namespace AgileCore\Models;

class Address extends Model  {

    public const STORAGE = "addresses";

    public const COLUMNS = [
        "id",
        "user_id",
        "firstname",
        "lastname",
        "address",
        "city",
        "zipcode",
        "country"
    ];

    public const CONDITIONS = [
        "user_id" => [FILTER_VALIDATE_INT, ['min_range' => 1]],
        "firstname" => "filterString",
        "lastname" => "filterString",
        "address" => "filterString",
        "city" => "filterString",
        "zipcode" => "filterZipcode",
        "country" => "filterCountry"
    ];

    var $user_id = -1;
    var $firstname = "";
    var $lastname = "";
    var $address = "";
    var $city = "";
    var $zipcode = "";
    var $country = "";

}
