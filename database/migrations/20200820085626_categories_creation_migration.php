<?php

use AgileCore\Models\Category;
use Phinx\Migration\AbstractMigration;

class CategoriesCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(Category::STORAGE)
            ->addColumn('name', 'string')
            ->addColumn('slug', 'string')
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->save();
    }

}
