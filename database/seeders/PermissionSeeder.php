<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

            $permissions = [
   
            'user.management',         
            'employee.management',    
            'rbac.management',         
            'dealer.management',       
            'visit.management',         
            'stock.management',         
            'order.management',         
            'payment.management',       
            'task.management',          
            'message.management',       
            'report.management',        
            'dashboard.access',         
            'setting.management',       
            'attachment.management',    
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
