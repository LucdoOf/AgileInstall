<?php

use AgileCore\Models\User;
use Phinx\Migration\AbstractMigration;

class UsersCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(User::STORAGE)
            ->addColumn('reference', 'string', ['limit' => 32])
            ->addColumn('firstname', 'string')
            ->addColumn('lastname', 'string')
            ->addColumn('mail', 'string', ['limit' => 1024])
            ->addColumn('password', 'string', ['limit' => 2048])
            ->addColumn('last_seen', 'datetime', ['null' => true])
            ->addColumn('inscription_date', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->save();
    }

}
