<?php

use AgileCore\Models\Page;
use Phinx\Migration\AbstractMigration;

class PagesCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(Page::STORAGE)
            ->addColumn('slug', 'string')
            ->addColumn('name', 'string', ['limit' => 1024])
            ->addColumn('static_title', 'string', ['null' => true, 'limit' => 1024])
            ->addColumn('static_description', 'string', ['null' => true, 'limit' => 2048])
            ->addColumn('static', 'boolean', ['default' => 1])
            ->save();
    }

}
