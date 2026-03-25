<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        $allowedStatuses = [
            OrderStatus::Pending->value,
            OrderStatus::Paid->value,
            OrderStatus::Shipped->value,
            OrderStatus::Cancelled->value,
        ];

        // Normalizza eventuali valori legacy/non validi prima dell'alter.
        DB::table('orders')
            ->whereNotIn('status', $allowedStatuses)
            ->update(['status' => OrderStatus::Pending->value]);

        if ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE orders MODIFY status ENUM('pending','paid','shipped','cancelled') NOT NULL DEFAULT 'pending'"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE orders MODIFY status VARCHAR(255) NOT NULL DEFAULT 'pending'"
            );
        }
    }
};
