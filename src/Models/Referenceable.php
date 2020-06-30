<?php

namespace AgileCore\Models;

use AgileCore\Utils\Dbg;

/**
 * Trait Referenceable
 * Must extend Model
 *
 * @package AgileCore\Models
 */
trait Referenceable {

    public $reference = "";

    public function hydrate($data)
    {
        parent::hydrate($data);
        $this->updateReference();
    }

    public function save() {
        $this->updateReference();
        parent::save();
    }

    private function updateReference() {
        if(!$this->exist() || !$this->isValid("reference")) {
            $this->reference = static::REFERENCE_PREFIX . '_' . n_digit_random(6);
            if (static::select(["reference" => $this->reference])->exist()) {
                $this->updateReference();
            }
        }
    }

    public function isValid($key = null){
        if($key == "reference"){
            if (!filter_var($this->{$key}, FILTER_CALLBACK, ['options' => 'validateReference'])) return $key;
            return true;
        } else {
            return parent::isValid($key);
        }
    }

}
