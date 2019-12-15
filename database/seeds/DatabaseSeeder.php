<?php
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();// para anular ciertas restricciones de seguridad
        $tablas =[
            'users',
        ];
        $this->truncateTablas($tablas);
        $this->call(UserSeeder::class);
        Model::reguard();// para reactivar las restricciones de seguridad
    }

    // aca verificamos que los roles no existan
    protected function truncateTablas(array $tablas){
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        // truncate elimina todos los datos de la base de datos
        foreach ($tablas as $tabla) {
            DB::table($tabla)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
    }
}
