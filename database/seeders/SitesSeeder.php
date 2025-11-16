<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

class SitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = [
            [
                'name' => 'Siège Principal Casablanca',
                'code' => 'SITE-CASA-01',
                'address' => 'Boulevard Hassan II, Casablanca',
                'city' => 'Casablanca',
                'country' => 'Maroc',
                'phone' => '+212 522 123 456',
                'email' => 'casablanca@fleetmanager.ma',
                'is_active' => true,
            ],
            [
                'name' => 'Agence Rabat',
                'code' => 'SITE-RABA-01',
                'address' => 'Avenue Mohammed V, Rabat',
                'city' => 'Rabat',
                'country' => 'Maroc',
                'phone' => '+212 537 123 456',
                'email' => 'rabat@fleetmanager.ma',
                'is_active' => true,
            ],
            [
                'name' => 'Agence Marrakech',
                'code' => 'SITE-MARR-01',
                'address' => 'Avenue Hassan II, Marrakech',
                'city' => 'Marrakech',
                'country' => 'Maroc',
                'phone' => '+212 524 123 456',
                'email' => 'marrakech@fleetmanager.ma',
                'is_active' => true,
            ],
            [
                'name' => 'Agence Tanger',
                'code' => 'SITE-TANG-01',
                'address' => 'Boulevard Pasteur, Tanger',
                'city' => 'Tanger',
                'country' => 'Maroc',
                'phone' => '+212 539 123 456',
                'email' => 'tanger@fleetmanager.ma',
                'is_active' => true,
            ],
            [
                'name' => 'Agence Agadir',
                'code' => 'SITE-AGAD-01',
                'address' => 'Avenue Hassan II, Agadir',
                'city' => 'Agadir',
                'country' => 'Maroc',
                'phone' => '+212 528 123 456',
                'email' => 'agadir@fleetmanager.ma',
                'is_active' => false, // Temporarily inactive
            ],
        ];

        foreach ($sites as $site) {
            Site::updateOrCreate(
                ['code' => $site['code']],
                $site
            );
        }
    }
}
