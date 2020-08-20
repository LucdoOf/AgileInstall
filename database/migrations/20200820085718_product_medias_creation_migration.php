<?php

use AgileCore\Models\ProductMedia;
use Phinx\Migration\AbstractMigration;

class ProductMediasCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(ProductMedia::STORAGE)
            ->addColumn('reference', 'string', ['limit' => 32])
            ->addColumn('product_id', 'integer')
            ->addColumn('name', 'string', ['limit' => 1024])
            ->addColumn('type', 'string')
            ->addColumn('url', 'string')
            ->addColumn('mime', 'string', ['limit' => 64])
            ->save();
    }

}
