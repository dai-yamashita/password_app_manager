<?php

use Phinx\Seed\AbstractSeed;

class Sitesettings extends AbstractSeed
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
      $data =array(
        array('key' => 'timezone', 'value' => 'UTC8'),
        array('key' => 'use_captcha', 'value' => 'no'),
        array('key' => 'isdaylightsaving', 'value' => 'no'),
        array('key' => 'account_expired_message', 'value' => 'Your account is already expired.'),
        array('key' => 'admin_email', 'value' => 'admin@mail.com'),
        array('key' => 'noreply_email', 'value' => 'noreply@mail.com')
      );
      $table = $this->table('sitesettings');
      $table->insert($data)
            ->save();
    }
}
