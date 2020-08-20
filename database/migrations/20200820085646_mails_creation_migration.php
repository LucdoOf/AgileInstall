<?php

use AgileCore\Models\Mail;
use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class MailsCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(Mail::STORAGE)
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('content', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('target', 'string')
            ->addColumn('sent_at', 'datetime', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('subject', 'string')
            ->addColumn('from_mail', 'string')
            ->addColumn('from_name', 'string')
            ->addColumn('basket_id', 'integer', ['null' => true])
            ->addColumn('command_id', 'integer', ['null' => true])
            ->addColumn('try_counter', 'integer')
            ->addColumn('not_before', 'datetime', ['null' => true])
            ->save();
    }

}
