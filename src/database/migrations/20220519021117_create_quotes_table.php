<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateQuotesTable extends AbstractMigration
{
    /**
     * Up the table
     *
     * @return void
     */
    public function up(): void
    {
        $this->table(with(new \App\Models\Quote())->getTable())
            ->addColumn('user_id', 'integer')
            ->addColumn('stock_id', 'integer')
            ->addColumn('date', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP', 'update' => '', 'timezone' => true, ])
            ->addColumn('open', 'float')
            ->addColumn('high', 'float')
            ->addColumn('low', 'float')
            ->addColumn('close', 'float')
            ->addTimestamps()
            ->addForeignKey('user_id', with(new \App\Models\User())->getTable(), 'id', [
                'delete'=> 'RESTRICT', 'update'=> 'RESTRICT'
            ])
            ->addForeignKey('stock_id', with(new \App\Models\Stock())->getTable(), 'id', [
                'delete'=> 'RESTRICT', 'update'=> 'RESTRICT'
            ])
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
