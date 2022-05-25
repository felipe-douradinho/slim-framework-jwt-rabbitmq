<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    /**
     * Up the table
     *
     * @return void
     */
    public function up(): void
    {
        $this->table( with(new \App\Models\User())->getTable() )
            ->addColumn('name', 'string')
            ->addColumn('email', 'string')
            ->addColumn('password', 'string')
            ->addColumn('access_token', 'string', [ 'null' => true, ] )
            ->addTimestamps()
            ->create();
    }

    /**
     * Drop the table
     *
     * @return void
     */
    public function down(): void
    {
        $this->table(with(new \App\Models\User())->getTable())->drop();
    }
}
