<?php

use Phinx\Migration\AbstractMigration;

class User extends AbstractMigration
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
      // create the table
      $table = $this->table('users');
      $table->addColumn('tmpid', 'integer')
            ->addColumn('username', 'string')
            ->addColumn('firstname', 'string')
            ->addColumn('lastname', 'string')
            ->addColumn('email', 'string')
            ->addColumn('skypeid', 'string')
            ->addColumn('position', 'string')
            ->addColumn('pwlength', 'string')
            ->create();
    }
}
