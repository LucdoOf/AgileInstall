<?php

use AgileCore\Models\Visit;
use Phinx\Migration\AbstractMigration;

class VisitsCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(Visit::STORAGE)
            ->addColumn('ip', 'string')
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('page_id', 'integer')
            ->addColumn('visit_date', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('visit_duration', 'integer')
            ->save();
    }

}
