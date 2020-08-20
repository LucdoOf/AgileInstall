<?php

use AgileCore\Models\Administrator;
use Phinx\Migration\AbstractMigration;

class AdministratorsCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(Administrator::STORAGE)
            ->addColumn('name', 'string')
            ->addColumn('password', 'string', ["limit" => 2048])
            ->addColumn('last_seen', 'datetime', ['null' => true])
            ->save();
    }

}
