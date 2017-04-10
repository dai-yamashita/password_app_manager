<?php

use Phinx\Seed\AbstractSeed;

class AccountType extends AbstractSeed
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
        'acctype' => 'Web',
        'desc' => 'sample description',
        'created' => time()
      );
      $table = $this->table('account_type');
      $table->insert($data)
            ->save();
    }
}
