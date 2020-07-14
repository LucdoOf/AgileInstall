<?php

namespace AgileCore\Models;

class ProductMedia extends Model {

    use Referenceable { isValid as public; }

    public const STORAGE = 'product_medias';
    public const REFERENCE_PREFIX = 'PMD';

    public const COLUMNS = [
      'id',
      'reference',
      'product_id',
      'name',
      'type',
      'mime',
      'url'
    ];

    public const CONDITIONS = [
        'product_id' => [FILTER_VALIDATE_INT, ['min_range' => 1]],
        'name' => 'filterString nullable',
        'type' => '',
        'mime' => '',
        'url' => FILTER_VALIDATE_URL
    ];

    public const MEDIA_TYPES = [
        'thumbnail' => ['jpg','png','svg'],
        'cover' => ['jpg','png','svg'],
        'other' => ['*']
    ];

    var $product_id = 0;
    var $name = null;
    var $type = '';
    var $url = '';
    var $mime = '';

    public function isValid($key = null, $excludedKeys = []) {
        if($key === 'type') {
            return array_key_exists($this->type, self::MEDIA_TYPES) ? true : $key;
        } elseif ($key === 'mime') {
            return $this->isValid('type') && (in_array(mime2ext($this->mime), self::MEDIA_TYPES[$this->type]) || in_array('*', self::MEDIA_TYPES[$this->type]));
        } elseif ($key === 'reference') {
            if (!self::testReference($this->{$key})) return $key;
            return true;
        } else {
            return parent::isValid($key, $excludedKeys);
        }
    }

}
