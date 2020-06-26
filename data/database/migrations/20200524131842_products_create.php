<?php

use AgileCore\Models\Product;
use Phinx\Migration\AbstractMigration;

class ProductsCreate extends AbstractMigration {

    public function change() {
        $this->table(Product::STORAGE)
            ->addColumn("reference", "string", ["limit" => 32])
            ->addColumn("stock", "integer", ["default" => 0])
            ->addColumn("price", "float")
            ->addColumn("boosted", "boolean", ["default" => false])
            ->addColumn("enabled", "boolean", ["default" => false])
            ->create();
    }

}
