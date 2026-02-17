<?php

namespace Database\Seeders;

use App\Models\Configuration;
use App\Models\Statut;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Boutique;
use App\Models\ModePaiment;
use App\Models\Pak;
use App\Models\Commande;
use App\Models\Companie;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Str;
use App\Models\Product;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Arr;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;




class ProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1) Création des rôles
        $roles = ['superadmin'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2) Définition des utilisateurs à créer
        $users = [
            [
                'name' => 'sirineshopping',
                'email' => 'sirineshopping@admin.tn',
                'password' => Hash::make('20202020'),
                'role' => 'superadmin',
            ],
            // ... ajoutez d’autres utilisateurs ici si besoin
        ];

        // 3) Création/mise à jour et assignation de rôle
        foreach ($users as $userData) {
            // Récupérer le rôle
            $roleName = $userData['role'];

            // On retire la clé 'role' pour ne pas la passer à fill()
            unset($userData['role']);

            // Création ou mise à jour
            $user = User::firstOrCreate(
                ['email' => $userData['email']],  // condition de recherche
                $userData                         // valeurs à insérer si absent
            );

            // On met à jour au cas où le password ou le statut aurait changé
            $user->fill($userData)->save();

            // Assignation du rôle (Spatie)
            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }
        }


        $statuts = [
            ['name' => 'Annulé', 'is_publish' => true],
            ['name' => 'Livrée et payée', 'is_publish' => true],
            ['name' => 'En cours de traitement', 'is_publish' => false],
            ['name' => 'En cours de livraison', 'is_publish' => false],
        ];
        foreach ($statuts as $statut) {
            Statut::create($statut);
        }

        $clients = [
            [
                'name' => 'Amina Ben Salah',
                'email' => 'amina.bensalah@example.com',
                'phone' => '+21620123456',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Mohamed Trabelsi',
                'email' => 'mohamed.trabelsi@example.com',
                'phone' => '+21621456789',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Leila Hammami',
                'email' => 'leila.hammami@example.com',
                'phone' => '+21622987654',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Ahmed Zarrouk',
                'email' => 'ahmed.zarrouk@example.com',
                'phone' => '+21620876543',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Sonia Chebil',
                'email' => 'sonia.chebil@example.com',
                'phone' => '+21620765432',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Walid Brahim',
                'email' => 'walid.brahim@example.com',
                'phone' => '+21621654321',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Farah Jaziri',
                'email' => 'farah.jaziri@example.com',
                'phone' => '+21620543210',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Tarek Ben Abdallah',
                'email' => 'tarek.abdallah@example.com',
                'phone' => '+21620321098',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Yasmine Fakhfakh',
                'email' => 'yasmine.fakhfakh@example.com',
                'phone' => '+21620210987',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Hatem Khlifi',
                'email' => 'hatem.khlifi@example.com',
                'phone' => '+21620109876',
                'adresse'=>'sousse'
            ],
        ];

        foreach ($clients as $data) {
            Client::create($data);
        }


        $settings = [
            [
                'site_name' => 'Paradis-deco',
                'site_logo' => 'logo.png',
                'support_email' => 'support@myawesomesite.com',
                'default_language' => 'fr',
                'currency' => 'USD',
                'meta_title' => 'Welcome to My Awesome Site',
                'meta_description' => 'This is an awesome site built with Laravel.',
                'shipping_cost' => 5.99,
                'free_shipping_threshold' => 50.00,
                'delivery_estimate_days' => 3,
                'maintenance_mode' => false,
                'homepage_banner' => 'banner.jpg',
            ],
        ];

        foreach ($settings as $setting) {
            Configuration::create($setting);
        }
    }
    }


