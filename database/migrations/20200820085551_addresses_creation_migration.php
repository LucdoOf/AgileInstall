<?php

use AgileCore\Models\Address;
use Phinx\Migration\AbstractMigration;

class AddressesCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(Address::STORAGE)
            ->addColumn('user_id', 'integer')
            ->addColumn('firstname', 'string')
            ->addColumn('lastname', 'string')
            ->addColumn('address', 'string', ['limit' => '2048'])
            ->addColumn('city', 'string')
            ->addColumn('zipcode', 'string', ['limit' => 8])
            ->addColumn('country', 'string', ['limit' => 2])
            ->save();
    }

}
