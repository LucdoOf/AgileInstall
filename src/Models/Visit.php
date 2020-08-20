<?php

namespace AgileCore\Models;

class Visit extends Model {

    const STORAGE = "visits";
    const NAME = "visit";

    const COLUMNS = [
        "id",
        "user_id",
        "ip",
        "page_id",
        "visit_date",
        "visit_duration"
    ];

    const CONDITIONS = [
        "user_id" => "filterStrictPositiveInt nullable",
        "ip" => "filterIp",
        "page_id" => "filterStrictPositiveInt",
        "visit_date" => "filterDate",
        "visit_duration" => "filterPositiveInt"
    ];

    var $user_id = null;
    var $ip = "";
    var $page_id = -1;
    var $visit_date = null;
    var $visit_duration = 0;
    var $page = null;
    var $user = null;

    /**
     * Retourne la page associée
     *
     * @return Page|null
     */
    public function linkedPage() {
        if(is_null($this->page)) $this->page = new Page($this->page_id);
        return $this->page->exist() ? $this->page : null;
    }

    /**
     * Retourne l'utilisateur associé
     *
     * @return User|null
     */
    public function linkedUser() {
        if(is_null($this->user)) $this->user = new User($this->user_id);
        return $this->user->exist() ? $this->user : null;
    }

    public function toArray($excludedKeys = []) {
        $parentArray = parent::toArray($excludedKeys);
        if(!in_array('page', $excludedKeys)) $parentArray['page'] = $this->linkedPage();
        if(!in_array('user', $excludedKeys)) $parentArray['user'] = $this->linkedUser();
        return $parentArray;
    }

}
