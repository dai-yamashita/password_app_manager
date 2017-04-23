<?php

use Phinx\Seed\AbstractSeed;

class Users extends AbstractSeed
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
          'tmpid' => 1,
          'username' => 'admin',
          'password' => '2751819b092e7d976bb47f8c56e0ec64',
          'clearpassword' => 'admin',
          'firstname' => 'admin',
          'lastname' => 'admin',
          'email' => 'admin@mail.com',
          'role_id' => 1,
        ),
        array(
          'tmpid' => 2,
          'username' => 'manager',
          'password' => '0fe49e03d86e6981be3fe4551075f3ba',
          'clearpassword' => 'manager',
          'firstname' => 'manager',
          'lastname' => 'manager',
          'email' => 'manager@mail.com',
          'role_id' => 2,
        ),
        array(
          'tmpid' => 3,
          'username' => 'user1',
          'password' => '038e2e4e4ba903e1c2a9a6824ebf104d',
          'clearpassword' => 'user1',
          'firstname' => 'user1',
          'lastname' => 'user1',
          'email' => 'user1@mail.com',
          'role_id' => 3,
        )
      );

      $table = $this->table('users');
      $table->insert($data)
            ->save();
    }
}
