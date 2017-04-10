<?php

use Phinx\Migration\AbstractMigration;

class Domains extends AbstractMigration
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
      $table = $this->table('domains', array('id' => 'domain_id'));
      $table->addColumn('project_id', 'integer')
            ->addColumn('type', 'string')
            ->addColumn('templateid', 'integer')
            ->addColumn('changefreq', 'string')
            ->addColumn('importance', 'string')
            ->addColumn('expirydate', 'integer', array('null' => true))
            ->addColumn('last_modified', 'integer', array('null' => true))
            ->addColumn('url', 'string')
            ->addColumn('loginurl', 'string')
            ->addColumn('username', 'string')
            ->addColumn('password', 'string')
            ->addColumn('pwlength', 'string')
            ->addColumn('mark', 'string')
            ->addColumn('notes', 'string')
            ->addColumn('customtemplate', 'text', array('null' => false))
            ->addColumn('created', 'integer')
            ->create();
    }
}
