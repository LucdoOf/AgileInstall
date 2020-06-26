<?php

namespace AgileCore\Models;

use AgileCore\Database\SQL;
use AgileCore\Utils\Dbg;
use DateTime;

/**
 * Trait Versionable
 * Must extend Model
 *
 * @package AgileCore\Models
 */
trait Versionable {

    public function versionableSave() {
        if($this->id > 0){
            $res = SQL::select(static::STORAGE, ["id" => $this->id]);
            if($res->rowCount() > 0){
                $newField = false;
                $values = [];
                $rec = $res->fetch(\PDO::FETCH_ASSOC);
                foreach ($rec as $col => $val){
                    $values[$col] = $val;
                    if($val !== $this->$col){
                        $newField = true;
                    }
                }
                if($newField){
                    $values['history_date'] = new DateTime();
                    $values['id'] = $this->id;
                    Dbg::logs("Creating new version of " . static::class);
                    Sql::insert(self::getHistoryTable(), $values);
                }
            }
        }
        return parent::save();
    }

    /**
     * @return static[]
     */
    public function getVersions() {
        $res = Sql::select(static::getHistoryTable(), ["id" => $this->id], "history_date DESC", null,null,[],[],null,true);
        $objs = [];
        while($rec = $res->fetch(\PDO::FETCH_ASSOC)){
            $obj = new static($rec);
            $objs[$rec['history_date']] = $obj;
        }
        return $objs;
    }

    private static function getHistoryTable(){
        return static::STORAGE."_history";
    }

}
