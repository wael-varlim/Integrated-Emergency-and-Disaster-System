<?php
// database/seeders/PermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define models and their CRUD operations
        $models = [
            'news',
            'report',
            'authority',
            'media',
            'post',
            'notification',
            'suggestion',
            'awareness_article',
            'region',
            'governorate',
            'city',
            'address',
            'news_type',
            'user',
        ];

        $actions = ['view_any', 'view', 'create', 'update', 'delete'];

        // Create permissions for each model
        foreach ($models as $model) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name'       => "{$action}_{$model}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Admin-only permissions
        Permission::firstOrCreate(['name' => 'manage_sub_admins', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_roles',      'guard_name' => 'web']);

        // ──────────────────────────────────────
        // Create Roles
        // ──────────────────────────────────────

        // 1. Super Admin - gets everything
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // 2. Posts Manager (Communication - Posts)
        $postsManager = Role::firstOrCreate(['name' => 'posts_manager', 'guard_name' => 'web']);
        $postsManager->syncPermissions([
            'view_any_post', 'view_post', 'create_post', 'update_post', 'delete_post',
            'view_any_media', 'view_media', 'create_media', 'update_media',
        ]);

        // 3. Awareness Manager (Communication - Awareness Articles)
        $awarenessManager = Role::firstOrCreate(['name' => 'awareness_manager', 'guard_name' => 'web']);
        $awarenessManager->syncPermissions([
            'view_any_awareness_article', 'view_awareness_article',
            'create_awareness_article', 'update_awareness_article', 'delete_awareness_article',
            'view_any_media', 'view_media', 'create_media', 'update_media',
        ]);

        // 4. Report Manager
        $reportManager = Role::firstOrCreate(['name' => 'report_manager', 'guard_name' => 'web']);
        $reportManager->syncPermissions([
            'view_any_report', 'view_report', 'update_report', 'delete_report',
            'view_any_region', 'view_region',
            'view_any_governorate', 'view_governorate',
            'view_any_city', 'view_city',
            'view_any_user', 'view_user', // Report managers need to see who reported
        ]);

        // 5. Suggestion Manager
        $suggestionManager = Role::firstOrCreate(['name' => 'suggestion_manager', 'guard_name' => 'web']);
        $suggestionManager->syncPermissions([
            'view_any_suggestion', 'view_suggestion', 'update_suggestion', 'delete_suggestion',
        ]);

        // 6. Authority Manager (Authorities + Notifications)
        $authorityManager = Role::firstOrCreate(['name' => 'authority_manager', 'guard_name' => 'web']);
        $authorityManager->syncPermissions([
            'view_any_authority', 'view_authority', 'create_authority', 'update_authority', 'delete_authority',
            'view_any_notification', 'view_notification', 'create_notification', 'update_notification', 'delete_notification',
        ]);

        // 7. Viewer (read-only)
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions(
            Permission::where('name', 'like', 'view_%')->pluck('name')->toArray()
        );

        // 8. User Viewer (only view users)
        $userViewer = Role::firstOrCreate(['name' => 'user_viewer', 'guard_name' => 'web']);
        $userViewer->syncPermissions([
            'view_any_user', 'view_user',
        ]);

        $this->command->info(' Permissions and Roles seeded successfully!');
        $this->command->table(
            ['Role', 'Permissions Count'],
            Role::all()->map(fn ($role) => [$role->name, $role->permissions->count()])
        );



        // Mobile User Role 
        $mobileUser = Role::firstOrCreate(['name' => 'mobile_user', 'guard_name' => 'web']);
        // $normalUser->syncPermissions([]);
    }
}