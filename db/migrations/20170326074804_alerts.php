<?php

use Phinx\Migration\AbstractMigration;

class Alerts extends AbstractMigration
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
      $table = $this->table('alerts', array('id' => 'alertid'));
      $table->addColumn('title', 'string', array('null' => false, 'limit' => 45))
            ->addColumn('isread', 'integer')
            ->addColumn('domainid', 'integer')
            ->addColumn('alert', 'text')
            ->addColumn('to', 'integer')
            ->addColumn('from', 'integer')
            ->addColumn('created', 'integer')
            ->create();
    }
}
