<?php

use Phinx\Migration\AbstractMigration;

class UserProfile extends AbstractMigration
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
      $table = $this->table('user_profile');
      $table->addColumn('user_id', 'string', array('null' => false, 'limit' => 32))
            ->addColumn('country', 'string', array('limit' => 20, 'null' => false))
            ->addColumn('website', 'string', array('limit' => 255, 'null' => false))
            ->create();
    }
}
