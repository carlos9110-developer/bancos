<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // usuario tipo cajero
        DB::table('users')->insert([
            'name' 	            =>	'Carlos Hincapie',
            'email'				=>	'carlos@hotmail.com',
            'password'	        =>  hash("SHA256",'12345'),
            'telefono'          =>  '3333335',
            'rol'               =>  '1'
        ]);

        // usuario tipo asesor comercial
        DB::table('users')->insert([
            'name' 	            =>	'Joan Ramirez',
            'email'				=>	'joan@hotmail.com',
            'password'	        =>	hash("SHA256",'12345'),
            'telefono'          =>  '3115455293',
            'rol'               =>  '2'
        ]);

    }
}
