<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $perms = [
                    ['name' => 'dashboard', 'display_name' => 'dashboard'],
                    ['name' => 'manage-user', 'display_name'=> 'manage-user'],
                    ['name' => 'menu', 'display_name'=> 'menu'],
                    ['name' => 'division', 'display_name'=> 'division'],
                    ['name' => 'department', 'display_name'=> 'department'],
                    ['name' => 'periode', 'display_name'=> 'periode'],
                    ['name' => 'customer', 'display_name'=> 'customer'],
                    ['name' => 'supplier', 'display_name'=> 'supplier'],
                    ['name' => 'part', 'display_name'=> 'part'],
                    ['name' => 'system', 'display_name'=> 'system'],
                    ['name' => 'item-category', 'display_name'=> 'item-category'],
                    ['name' => 'item', 'display_name'=> 'item'],
                    ['name' => 'upload-po', 'display_name'=> 'upload-po'],
                    ['name' => 'gr-confirm', 'display_name'=> 'gr-confirm'],
                    ['name' => 'eps-tracking', 'display_name'=> 'eps-tracking'],
                    ['name' => 'budget_upload', 'display_name'=> 'budget_upload'],
                    ['name' => 'upload-bom-finish-good', 'display_name'=> 'upload-bom-finish-good'],
                    ['name' => 'uppload-sales-data', 'display_name'=> 'uppload-sales-data'],
                    ['name' => 'upload-bom-semi-finish-good', 'display_name'=> 'upload-bom-semi-finish-good'],
                    ['name' => 'upload-master-price-part', 'display_name'=> 'upload-master-price-part'],
                    ['name' => 'output-master', 'display_name'=> 'output-master'],
                    ['name' => 'role', 'display_name'=> 'role'],
                    ['name' => 'user-role', 'display_name'=> 'user-role'],
                    ['name' => 'sap-asset', 'display_name'=> 'sap-asset'],
                    ['name' => 'sap-cost-center', 'display_name'=> 'sap-cost-center'],
                    ['name' => 'sap-gl-account', 'display_name'=> 'sap-gl-account'],
                    ['name' => 'sap-number', 'display_name'=> 'sap-number'],
                    ['name' => 'sap-taxes', 'display_name'=> 'sap-taxes'],
                    ['name' => 'sap-uom', 'display_name'=> 'sap-uom'],
                    ['name' => 'sap-vendor', 'display_name'=> 'sap-vendor'],
                    ['name' => 'link-to-sap', 'display_name'=> 'link-to-sap'],
                    ['name' => 'approval', 'display_name'=> 'approval'],
                    ['name' => 'list-capex', 'display_name'=> 'list-capex'],
                    ['name' => 'list-approval-capex', 'display_name'=> 'list-approval-capex'],
                    ['name' => 'pending-approval-capex', 'display_name'=> 'pending-approval-capex'],
                    ['name' => 'create-approval-capex', 'display_name'=> 'create-approval-capex'],
                    ['name' => 'upload-capex', 'display_name'=> 'upload-capex'],
                    ['name' => 'archive-capex', 'display_name'=> 'archive-capex'],
                    ['name' => 'closing-capex', 'display_name'=> 'closing-capex'],
                    ['name' => 'fyear_closing', 'display_name'=> 'fyear_closing'],
                    ['name' => 'cip-admin-capex', 'display_name'=> 'cip-admin-capex'],
                    ['name' => 'cip-settlement-capex', 'display_name'=> 'cip-settlement-capex'],
                    ['name' => 'list-expense', 'display_name'=> 'list-expense'],
                    ['name' => 'list-approval-expense', 'display_name'=> 'list-approval-expense'],
                    ['name' => 'pending-approval-expense', 'display_name'=> 'pending-approval-expense'],
                    ['name' => 'create-approval-expense', 'display_name'=> 'create-approval-expense'],
                    ['name' => 'upload-expense', 'display_name'=> 'upload-expense'],
                    ['name' => 'closing-expense', 'display_name'=> 'closing-expense'],
                    ['name' => 'archive-expense', 'display_name'=> 'archive-expense'],
                    ['name' => 'list-approval-unbudget', 'display_name'=> 'list-approval-unbudget'],
                    ['name' => 'create-approval-unbudget', 'display_name'=> 'create-approval-unbudget'],
                    ['name' => 'pending-approval-unbudget', 'display_name'=> 'pending-approval-unbudget']
                ];
        
        foreach ($perms as $perm) {
            Permission::create($perm);
        }
    }
}
