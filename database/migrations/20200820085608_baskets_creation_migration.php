<?php

use AgileCore\Models\Basket;
use Phinx\Migration\AbstractMigration;

class BasketsCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(Basket::STORAGE)
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('ip', 'string')
            ->save();
    }

}
