<?php


use Phinx\Seed\AbstractSeed;

class QuotesTableSeeder extends AbstractSeed
{
    public function getDependencies()
    {
        return [
            'UsersTableSeeder',
            'StocksTableSeeder',
        ];
    }

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
                    'user_id'   => 1,//, \App\Models\Quote::first()->id,
                    'stock_id'  => 1,//, \App\Models\Quote::first()->id,
                    'open'      => 123.66,
                    'high'      => 127.66,
                    'low'       => 122.49,
                    'close'     => 121.89,
                ],
            ];

            $table = $this->table( with(new \App\Models\Quote())->getTable() );

            $table->truncate();

            for($i=0; $i < 20; $i++) {
                $table->insert($data)->saveData();
            }

        }
    }
}
