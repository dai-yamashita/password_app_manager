<?php

use Phinx\Migration\AbstractMigration;

class GroupDomains extends AbstractMigration
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
      $table = $this->table('group_domains');
      $table->addColumn('domain_id', 'integer', array('null' => false, 'limit' => 11))
            ->addColumn('deptid', 'integer', array('null' => false, 'limit' => 11))
            ->create();
    }
}
