<?php

use Phinx\Migration\AbstractMigration;

class CiSessions extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
      $table = $this->table('ci_sessions');
      $table->addColumn('ip_address', 'string', array('null' => false, 'limit' => 45))
            ->addColumn('last_activity', 'integer', array('null' => false, 'limit' => 12))
            ->addColumn('user_agent', 'string', array('null' => true, 'limit' => 255))
            ->addColumn('session_id', 'string', array('null' => true, 'limit' => 255))
            ->addColumn('user_data', 'text', array('null' => true))
            ->addIndex(array('session_id'), array('unique' => true, 'name' => 'ci_sessions_session_id'))
            ->create();
    }
}
