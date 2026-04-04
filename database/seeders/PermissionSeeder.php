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

        // 2. News Manager
        $newsManager = Role::firstOrCreate(['name' => 'news_manager', 'guard_name' => 'web']);
        $newsManager->syncPermissions([
            'view_any_news', 'view_news', 'create_news', 'update_news', 'delete_news',
            'view_any_news_type', 'view_news_type',
            'view_any_media', 'view_media', 'create_media', 'update_media',
        ]);

        // 3. Report Manager
        $reportManager = Role::firstOrCreate(['name' => 'report_manager', 'guard_name' => 'web']);
        $reportManager->syncPermissions([
            'view_any_report', 'view_report', 'update_report', 'delete_report',
            'view_any_region', 'view_region',
            'view_any_governorate', 'view_governorate',
            'view_any_city', 'view_city',
        ]);

        // 4. Content Manager
        $contentManager = Role::firstOrCreate(['name' => 'content_manager', 'guard_name' => 'web']);
        $contentManager->syncPermissions([
            'view_any_post', 'view_post', 'create_post', 'update_post', 'delete_post',
            'view_any_awareness_article', 'view_awareness_article',
            'create_awareness_article', 'update_awareness_article', 'delete_awareness_article',
            'view_any_media', 'view_media', 'create_media', 'update_media',
        ]);

        // 5. Authority Manager
        $authorityManager = Role::firstOrCreate(['name' => 'authority_manager', 'guard_name' => 'web']);
        $authorityManager->syncPermissions([
            'view_any_authority', 'view_authority', 'create_authority', 'update_authority', 'delete_authority',
            'view_any_notification', 'view_notification', 'create_notification',
        ]);

        // 6. Viewer (read-only)
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions(
            Permission::where('name', 'like', 'view_%')->pluck('name')->toArray()
        );

        $this->command->info(' Permissions and Roles seeded successfully!');
        $this->command->table(
            ['Role', 'Permissions Count'],
            Role::all()->map(fn ($role) => [$role->name, $role->permissions->count()])
        );
    }
}