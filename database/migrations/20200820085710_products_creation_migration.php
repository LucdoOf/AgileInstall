<?php

use AgileCore\Models\Product;
use Phinx\Migration\AbstractMigration;

class ProductsCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(Product::STORAGE)
            ->addColumn('reference', 'string', ['limit' => 32])
            ->addColumn('name', 'string', ['limit' => 1024])
            ->addColumn('category_id', 'integer')
            ->addColumn('description', 'string', ['limit' => 2048])
            ->addColumn('stock', 'integer')
            ->addColumn('price', 'float')
            ->addColumn('boosted', 'boolean', ['default' => false])
            ->addColumn('enabled', 'boolean', ['default' => false])
            ->save();
    }

}
