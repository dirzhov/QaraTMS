<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DropTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'droptables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop off all tables';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach(\DB::select('SHOW TABLES') as $table) {
            $table_array = get_object_vars($table);
            \Schema::drop($table_array[key($table_array)]);
        }
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}