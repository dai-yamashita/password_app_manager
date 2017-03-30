<?php

use Phinx\Migration\AbstractMigration;

class UserTemp extends AbstractMigration
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
      $table = $this->table('user_temp');
      $table->addColumn('username', 'string', array('null' => false, 'limit' => 255))
            ->addColumn('password', 'integer', array('null' => false, 'limit' => 34))
            ->addColumn('email', 'string', array('null' => false, 'limit' => 100))
            ->addColumn('activation_key', 'string', array('null' => false, 'limit' => 50))
            ->addColumn('last_ip', 'string', array('null' => false, 'limit' => 40))
            ->addColumn('created', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->create();
    }
}
