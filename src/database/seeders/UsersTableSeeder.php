<?php


use Firebase\JWT\JWT;
use Phinx\Seed\AbstractSeed;

class UsersTableSeeder extends AbstractSeed
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
            $payload = [ 'user_id' => 1 ];

            $data = [
                [
                    'name'              => 'Felipe',
                    'email'             => 'ddddddd@gmail.com',
                    'password'          => 'admin',
                    'access_token'      => JWT::encode( $payload, env('JWT_SECRET') ),
                ],
            ];

            $table = $this->table( with(new \App\Models\User())->getTable() );

            $this->execute('SET unique_checks=0; SET foreign_key_checks=0;');
            $table->truncate();
            $this->execute('SET unique_checks=1; SET foreign_key_checks=1;');

            $table->insert($data)->saveData();
        }
    }
}
