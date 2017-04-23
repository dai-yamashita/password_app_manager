<?php

use Phinx\Migration\AbstractMigration;

class PersonalDomains extends AbstractMigration
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
      $table = $this->table('personal_domains', array('id' => 'domain_id'));
      $table->addColumn('project_id', 'integer')
            ->addColumn('type', 'integer')
            ->addColumn('templateid', 'integer')
            ->addColumn('changefreq', 'string')
            ->addColumn('importance', 'string')
            ->addColumn('url', 'string')
            ->addColumn('loginurl', 'string')
            ->addColumn('username', 'string')
            ->addColumn('password', 'string')
            ->addColumn('pwlength', 'string')
            ->addColumn('mark', 'text')
            ->addColumn('notes', 'text')
            ->addColumn('customtemplate', 'text')
            ->addColumn('expirydate', 'integer')
            ->addColumn('last_modified', 'integer')
            ->addColumn('created', 'integer')
            ->create();
    }
}
