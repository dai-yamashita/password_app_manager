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
        'tmpid' => 1,
        'username' => 'admin',
        'password' => '74be16979710d4c4e7c6647856088456',
        'firstname' => 'admin',
        'lastname' => 'admin',
        'email' => 'admin@mail.com',
        'skypeid' => 'admin',
        'role_id' => 2,
        'position' => '',
      );
      $table = $this->table('users');
      $table->insert($data)
            ->save();
    }
}
