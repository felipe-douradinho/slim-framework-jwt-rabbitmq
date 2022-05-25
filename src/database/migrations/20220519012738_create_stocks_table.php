<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateStocksTable extends AbstractMigration
{
    /**
     * Up the table
     *
     * @return void
     */
    public function up(): void
    {
        $this->table(with(new \App\Models\Stock())->getTable())
            ->addColumn('name', 'string')
            ->addColumn('symbol', 'string')
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
        $this->table(with(new \App\Models\Stock())->getTable())->drop();
    }
}
