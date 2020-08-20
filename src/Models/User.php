<?php

namespace AgileCore\Models;

class User extends Model {

    use Referenceable;

    public const STORAGE = "users";
    public const REFERENCE_PREFIX = "USR";
    public const NAME = 'user';

    public const COLUMNS = [
        "id",
        "reference",
        "firstname",
        "lastname",
        "mail",
        "password",
        "last_seen",
        "inscription_date"
    ];

    public const CONDITIONS = [
        "firstname" => "filterString",
        "lastname" => "filterString",
        "mail" => FILTER_VALIDATE_EMAIL,
        "last_seen" => "filterDate nullable",
        "inscription_date" => "filterDate"
    ];

    var $firstname = "";
    var $lastname = "";
    var $mail = "";
    var $password = "";
    var $last_seen = null;
    var $inscription_date = null;

    /**
     * Retourne la liste des adresses associées à l'utilisateur
     *
     * @return Address[]
     */
    public function getLinkedAddresses() {
        return Address::getAll(["user_id" => $this->id]);
    }

    /**
     * Retourne la liste des mails envoyés à cet utilisateur
     *
     * @return Mail[]
     */
    public function getSentMails() {
        return Mail::getAll(["user_id" => $this->id]);
    }

    public function toArray($excludedKeys = []) {
        $parentArray = parent::toArray($excludedKeys);
        $parentArray['linked_addresses'] = Model::listToArray($this->getLinkedAddresses());
        $parentArray['sent_mails'] = Model::listToArray($this->getSentMails());
        return $parentArray;
    }

}
