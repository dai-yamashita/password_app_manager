<?php

use Phinx\Seed\AbstractSeed;

class Domains extends AbstractSeed
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
          'project_id' => 1,
          'type' => 1,
          'templateid' => -100,
          'mark' => '',
          'notes' => '',
          'customtemplate' => '',
          'changefreq' => 'hourly',
          'importance' => 'critical',
          'expirydate' => time(),
          'url' => 'mysite1.com',
          'loginurl' => 'mysite1.com/login.php',
          'username' => 'user1',
          'password' => 'user1',
          'pwlength' => '8',
          'created' => time()
        ),
        array(
          'project_id' => 2,
          'type' => 1,
          'templateid' => -100,
          'mark' => '',
          'notes' => '',
          'customtemplate' => '',
          'changefreq' => 'hourly',
          'importance' => 'critical',
          'expirydate' => time(),
          'url' => 'mysite2.com',
          'loginurl' => 'mysite2.com/login.php',
          'username' => 'user1',
          'password' => 'user1',
          'pwlength' => '8',
          'created' => time()
        )
      );
    ;
      $table = $this->table('domains');
      $table->insert($data)
            ->save();
    }
}
