<?php

use Phinx\Seed\AbstractSeed;

class Permissions extends AbstractSeed
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
        array(
          'role_id' => 1,
          'data' => 'a:1:{s:3:"uri";a:7:{i:0;s:8:"/domain/";i:1;s:10:"/mydomain/";i:2;s:12:"/department/";i:3;s:13:"/accounttype/";i:4;s:6:"/user/";i:5;s:9:"/project/";i:6;s:10:"/settings/";}}',
        ),
        array(
          'role_id' => 2,
          'data' => 'a:1:{s:3:"uri";a:5:{i:0;s:8:"/domain/";i:1;s:10:"/mydomain/";i:2;s:12:"/department/";i:3;s:6:"/user/";i:4;s:9:"/project/";}}',
        ),
        array(
          'role_id' => 3,
          'data' => 'a:1:{s:3:"uri";a:2:{i:0;s:10:"/mydomain/";i:1;s:8:"/domain/";}}',
        )
      );

      $table = $this->table('permissions');
      $table->insert($data)
            ->save();
    }
}
