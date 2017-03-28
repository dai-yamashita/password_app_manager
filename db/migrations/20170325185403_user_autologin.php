<?php

use Phinx\Migration\AbstractMigration;

class UserAutologin extends AbstractMigration
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
      $table = $this->table('user_autologin', array('id' => false, 'primary_key' => array('key_id', 'user_id')));
      $table->addColumn('key_id', 'string', array('null' => false, 'limit' => 32))
            ->addColumn('user_id', 'integer', array('null' => false, 'default' => 0))
            ->addColumn('user_agent', 'string')
            ->addColumn('last_ip', 'string', array('limit' => 40, 'null' => false))
            ->addColumn('last_login', 'timestamp')
            ->create();
    }
}
