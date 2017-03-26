<?php

use Phinx\Seed\AbstractSeed;

class Roles extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
      $data = array(
        array('id' => 1, 'name' => 'Administrator', 'parent_id' => 0),
        array('id' => 2, 'name' => 'Owner', 'parent_id' => 0),
      );
      $table = $this->table('roles');
      $table->insert($data)
            ->save();
    }
}
