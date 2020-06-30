<?php

namespace AgileCore\Models;

use AgileCore\Database\SQL;
use AgileCore\Utils\Dbg;
use AgileCore\Utils\Plural;
use ArrayObject;
use DateTime;
use PDO;
use PDOStatement;
use function Sodium\compare;

abstract class Model {

    const STORAGE = "";
    const SQL_AS = "";
    const SQL_JOINS = [];
    const COLUMNS = [];
    const CONDITIONS = [];
    const PAGE_LIMIT = 15;

    var $id = 0;
    var $additional_data = [];

    /**
     * Model constructor.
     * @param array|int $data Données sous forme de tableau associatif
     */
    public function __construct($data = []){
        if(is_array($data)){
            $this->hydrate($data);
        } else {
            $id = (int)$data;
            if ($id > 0) {
                $this->id = $id;
                $this->load();
            }
        }
    }

    /**
     * Récupère des données et en base et hydrate l'objet
     */
    private function load(){
        $req = SQL::select($this::STORAGE, ["id" => $this->id]);
        if($req->rowCount() > 0){
            $this->hydrate($req->fetch(PDO::FETCH_ASSOC));
        } else {
            $this->id = 0;
        }
    }

    /**
     * Sauvegarde l'objet en base ou l'insère en fonction de son identifiant
     */
    public function save(){
        if(!$this->exist()){
            $this->id = SQL::insert($this::STORAGE, $this->toInitialArray());
        } else {
            SQL::update($this::STORAGE, $this->toInitialArray(), ["id" => $this->id]);
        }
    }

    /**
     * Supprime l'objet en base
     */
    public function delete(){
        if($this->id > 0){
            SQL::delete($this::STORAGE, ["id" => $this->id]);
        }
    }

    /**
     * Array de sauvegarde SQL
     *
     * @return array
     */
    private function toInitialArray(){
        $values = [];
        if(!empty($this::COLUMNS)){
            foreach ($this::COLUMNS as $column){
                $values[$column] = $this->{$column};
            }
        }
        return $values;
    }

    /**
     * Retourne la liste des propriétés de l'objet listées dans la constante columns
     *
     * @return array
     */
    public function toArray(){
        $values = self::toInitialArray();
         foreach ($this::COLUMNS as $column) {
             if ($this->{$column} instanceof DateTime) {
                 $values[$column . "_stamp"] = $this->{$column}->getTimestamp();
             }
         }
         foreach ($this->additional_data as $k => $v) {
             if ($v instanceof DateTime) {
                 $this->additional_data[$k . "_stamp"] = $v->getTimestamp();
             }
         }
        $values["additional_data"] = $this->additional_data;
        return $values;
    }

    /**
     * Hydrate l'objet en fonction d'une array associative
     * @param $data []
     */
    public function hydrate($data){
        if($data != false){
            foreach ($data as $key => $value){
                if(in_array($key, array_values(static::COLUMNS)) && property_exists($this, $key)) {
                    if(is_bool($this->{$key}) && !is_bool($value)) {
                        $this->{$key} = $value == 1 ? true : false;
                    } else if(!is_null(filterDate($value))){
                        $this->{$key} = filterDate($value);
                    } else {
                        $this->{$key} = $value;
                    }
                } else {
                    if(!is_null(filterDate($value))){
                        $this->additional_data[$key] = filterDate($value);
                    } else {
                        $this->additional_data[$key] = $value;
                    }
                }
            }
        }
    }

    /**
     * Retourne la liste des objets concernés
     * @param array|string $where
     * @param null $order
     * @param null $limit
     * @param null $offset
     * @param array $join_tables
     * @param array $additionnalSelect
     * @param null $customWhere
     * @return static[]
     */
    public static function getAll($where = [], $order = null, $limit = null, $offset = null, $join_tables = [], $additionnalSelect = [], $customWhere = null){
        return SQL::instantiateAll(SQL::select(static::STORAGE, $where, $order, $limit, $offset, $join_tables, $additionnalSelect, $customWhere), static::class);
    }

    /**
     * Retourne la liste des objets paginés
     * @param $page
     * @param null $order
     * @param array $filter
     * @return static[]
     */
    public static function page($page, $order=null, $filter = []){
        $joinStr = "";
        foreach (static::SQL_JOINS as $join){
            $class1 = array_keys($join)[0];
            $class1As = Plural::singularize($class1::STORAGE);
            $var1 = $class1 == static::class ? $join[$class1] : Plural::singularize($class1::STORAGE) . "." . $join[$class1];

            $class2 = array_keys($join)[1];
            $var2 = $class2 == static::class ? $join[$class2] : Plural::singularize($class2::STORAGE) . "." . $join[$class2];

            $joinStr .= " LEFT JOIN " . $class1::STORAGE . " AS $class1As ON $var1 = $var2 ";
        }
        return static::getAll($filter, $order, static::PAGE_LIMIT, $page*static::PAGE_LIMIT, $joinStr);
    }

    /**
     * Recherche un objet avec une clef
     * @param $query
     * @param null $order
     * @param null $limit
     * @param null $offset
     * @return static[]
     */
    public static function search($query, $order = null, $limit = null, $offset = null){
        return SQL::instantiateAll(SQL::search(static::STORAGE, array_keys(static::COLUMNS), $query, $order, $limit, $offset), static::class);
    }

    /**
     * Fait une requête select sur la table de l'objet avec les conditions sélectionnées
     * @param $where
     * @param null $order
     * @param null $limit
     * @param null $offset
     * @param array $join_tables
     * @param array $additionnalSelect
     * @return static
     */
    public static function select($where, $order = null, $limit = null, $offset = null, $join_tables = [], $additionnalSelect = []){
        return new static(SQL::select(static::STORAGE, $where, $order, $limit, $offset, $join_tables, $additionnalSelect)->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * Met a jour un objet ou une liste d'objet
     * @param $data
     * @param $where
     * @return false|PDOStatement
     */
    public static function update($data, $where){
        return SQL::update(static::STORAGE, $data, $where);
    }

    /**
     * Compte le nombre d'objet présent en base
     * @param $where
     * @param array $join_tables
     * @return mixed
     */
    public static function count($where = [], $join_tables = []){
        return SQL::select(static::STORAGE, $where, null, null, null, $join_tables, ["count(".static::STORAGE.".id) AS counter"])->fetch(PDO::FETCH_ASSOC)["counter"];
    }

    /**
     * Retourne le dernier objet créé en base
     * @return static
     */
    public static function getLatest(){
        return new static(SQL::selectMax(static::STORAGE, "id")->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * Vérifie l'existence d'un Model
     * @return bool
     */
    public function exist(){
        return $this->id > 0;
    }

    /**
     * Retourne true si un l'objet est valide selon la constante conditions, sinon retourne le nom du champ qui ne va pas
     */
    public function isValid($key = null){
        if(is_null($key)){
            foreach (static::CONDITIONS as $key => $condition) {
                if ($this->isValid($key) !== true) return $key;
            }
            return true;
        } else {
            $condition = static::CONDITIONS[$key];
            if (is_string($condition) || is_int($condition)) {
                if (startsWith($condition, "/") && endsWith($condition, "/")) {
                    if (!preg_match($condition, $this->{$key})) return $key;
                } else {
                    if (is_int($condition)) {
                        if (is_null(filter_var($this->{$key}, $condition, ['flags' => FILTER_NULL_ON_FAILURE]))) return $key;
                    } else {
                        $params = explode(" ", $condition);
                        if(in_array("nullable", $params)){
                            if (!is_null($this->{$key}) && is_null(filter_var($this->{$key}, FILTER_CALLBACK, ['options' => $params[0], 'flags' => FILTER_NULL_ON_FAILURE]))) return $key;
                        } else {
                            if (is_null(filter_var($this->{$key}, FILTER_CALLBACK, ['options' => $condition, 'flags' => FILTER_NULL_ON_FAILURE]))) return $key;
                        }
                    }
                }
            } else if(is_array($condition)) {

                $options = array(
                    'options' => $condition[1],
                    'flags' => FILTER_NULL_ON_FAILURE
                );

                if(is_null(filter_var($this->{$key}, (int)$condition[0], $options))) return $key;
            } else {
                Dbg::logs("Unknow type of condition " . $condition);
            }
        }
        return true;
    }

    /**
     * Retourne une liste de to array
     *
     * @param $objArray static[]
     * @return array
     */
    static function listToArray($objArray){
        $toReturn = [];
        foreach ($objArray as $item){
            $toReturn[] = $item->toArray();
        }
        return $toReturn;
    }

}
