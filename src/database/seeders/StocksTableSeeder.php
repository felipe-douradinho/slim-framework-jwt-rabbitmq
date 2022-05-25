<?php


use Phinx\Seed\AbstractSeed;

class StocksTableSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        // -- prevents if non DEV env
        if( env('ENV', 'prod') == 'dev' )
        {
            $data = [
                [
                    'name'      => 'APPLE',
                    'symbol'    => 'aapl.us',
                ],
            ];

            $table = $this->table( with(new \App\Models\Stock())->getTable() );

            $this->execute('SET unique_checks=0; SET foreign_key_checks=0;');
            $table->truncate();
            $this->execute('SET unique_checks=1; SET foreign_key_checks=1;');

            $table->insert($data)->saveData();
        }
    }
}
