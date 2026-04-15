<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Réinitialiser le cache des rôles et permissions de Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Définir la liste des permissions par module
        $permissions = [
            // --- PARAMÉTRAGES (Settings) ---
            'view company_settings', 'update company_settings',
            'view sites', 'create sites', 'update sites', 'delete sites',
            'view buildings', 'create buildings', 'update buildings', 'delete buildings',
            'view animal_types', 'create animal_types', 'update animal_types', 'delete animal_types',
            'view breeds', 'create breeds', 'update breeds', 'delete breeds',
            // --- ZOOTECHNIE (Lots & Relevés) ---
            'view flocks','create flocks','update flocks','delete flocks','submit flocks','approve flocks','reject flocks','end flocks',
            'view daily_records', 'create daily_records', 'update daily_records', 'delete daily_records', 'approve daily_records',
            'view weight_records', 'create weight_records',
            // --- PROVENDERIE & LOGISTIQUE ---
            'view recipes', 'create recipes', 'update recipes',
            'view feed_productions', 'create feed_productions', 'approve feed_productions',
            'view inventory', 'adjust inventory',
            // --- FINANCES & COMPTABILITÉ ---
            'view invoices', 'create invoices', 'update invoices', 'approve invoices',
            'view payments', 'create payments',
            'view accounting_rules', 'create accounting_rules', 'update accounting_rules',
            'view reports', // Accès au cockpit BI
            // --- SÉCURITÉ ---
            'view users', 'create users', 'update users', 'delete users',
            'view roles', 'update roles',
            'view activity_logs'
        ];

        // 3. Créer les permissions dans la base de données
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 4. Créer les Rôles et leur assigner les permissions

        // --- RÔLE 1 : SECRÉTAIRE (Focus sur la saisie de données) ---
        $roleSecretary = Role::firstOrCreate(['name' => 'Secretary']);
        $roleSecretary->givePermissionTo([
            'view flocks',
            'view daily_records', 'create daily_records', 'update daily_records', // Peut saisir et corriger ses propres saisies
            'view weight_records', 'create weight_records',
            'view invoices', 'create invoices',
            'view payments', 'create payments',
        ]);

        // --- RÔLE 2 : GESTIONNAIRE / MANAGER (Focus sur le contrôle et la validation) ---
        $roleManager = Role::firstOrCreate(['name' => 'Manager']);
        $roleManager->givePermissionTo(Permission::all()); // On lui donne tout...
        $roleManager->revokePermissionTo([
            // ...sauf la gestion de la sécurité pure et de la configuration de l'entreprise
            'view company_settings', 'update company_settings',
            'view users', 'create users', 'update users', 'delete users',
            'view roles', 'update roles',
            'view activity_logs'
        ]);

        // --- RÔLE 3 : SUPER-ADMIN (Propriétaire) ---
        // Note: Le Super-Admin n'a pas besoin de permissions explicites car on va 
        // intercepter toutes les vérifications via un Gate (voir étape 3 ci-dessous).
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super-Admin']);

        // 5. Créer les utilisateurs de test (pour tes 3 machines)

        // L'utilisateur Propriétaire (Super-Admin)
        $superAdmin = User::firstOrCreate(
            ['email' => 'proprio@ferme.com'],
            [
                'name' => 'Le Propriétaire',
                'password' => Hash::make('password'),
            ]
        );
        if (!$superAdmin->hasRole('Super-Admin')) {
            $superAdmin->assignRole($roleSuperAdmin);
        }

        // L'utilisateur Gestionnaire
        $manager = User::firstOrCreate(
            ['email' => 'manager@ferme.com'],
            [
                'name' => 'Le Gestionnaire',
                'password' => Hash::make('password'),
            ]
        );
        if (!$manager->hasRole('Manager')) {
            $manager->assignRole($roleManager);
        }

        // L'utilisateur Secrétaire
        $secretary = User::firstOrCreate(
            ['email' => 'secretaire@ferme.com'],
            [
                'name' => 'La Secrétaire',
                'password' => Hash::make('password'),
            ]
        );
        if (!$secretary->hasRole('Secretary')) {
            $secretary->assignRole($roleSecretary);
        }
    }
}