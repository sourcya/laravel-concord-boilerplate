<?php

use Illuminate\Database\Seeder;

class BoilerplatePermissionsTableSeeder extends Seeder
{
/**
* Run the database seeds.
*
* @return void
*/
public function run()
{
    $permissions = array(
		array('id' => 0,'guard_name' => 'api' ,'name' => 'View User'),
		array('id' => 0,'guard_name' => 'api' ,'name' => 'Create User'),
		array('id' => 0,'guard_name' => 'api' ,'name' => 'Edit User'),
		array('id' => 0,'guard_name' => 'api' ,'name' => 'Delete User'),
//		array('id' => 0,'guard_name' => 'api','name' => "View Category"),
//		array('id' => 0,'guard_name' => 'api','name' => "Create Category"),
//		array('id' => 0,'guard_name' => 'api','name' => "Edit Category"),
//		array('id' => 0,'guard_name' => 'api','name' => "Delete Category"),
//		array('id' => 0,'guard_name' => 'api','name' => "View Product"),
//		array('id' => 0,'guard_name' => 'api','name' => "Create Product"),
//		array('id' => 0,'guard_name' => 'api','name' => "Edit Product"),
//		array('id' => 0,'guard_name' => 'api','name' => "Delete Product"),
//		array('id' => 0,'guard_name' => 'api','name' => "View Meta"),
//		array('id' => 0,'guard_name' => 'api','name' => "Create Meta"),
//		array('id' => 0,'guard_name' => 'api','name' => "Edit Meta"),
//		array('id' => 0,'guard_name' => 'api','name' => "Delete Meta"),
//		array('id' => 0,'guard_name' => 'api','name' => "View Shipping"),
//		array('id' => 0,'guard_name' => 'api','name' => "Create Shipping"),
//		array('id' => 0,'guard_name' => 'api','name' => "Edit Shipping"),
//		array('id' => 0,'guard_name' => 'api','name' => "Delete Shipping"),
//		array('id' => 0,'guard_name' => 'api','name' => "View Order"),
//		array('id' => 0,'guard_name' => 'api','name' => "Create Order"),
//		array('id' => 0,'guard_name' => 'api','name' => "Edit Order"),
//		array('id' => 0,'guard_name' => 'api','name' => "Delete Order"),
        array('id' => 0,'guard_name' => 'api','name' => "View Agent"),
        array('id' => 0,'guard_name' => 'api','name' => "Create Agent"),
        array('id' => 0,'guard_name' => 'api','name' => "Edit Agent"),
        array('id' => 0,'guard_name' => 'api','name' => "Delete Agent"),
        array('id' => 0,'guard_name' => 'api','name' => "Handling Agent Requests"),
//        array('id' => 0,'guard_name' => 'api','name' => "View Logistic"),
//        array('id' => 0,'guard_name' => 'api','name' => "Create Logistic"),
//        array('id' => 0,'guard_name' => 'api','name' => "Edit Logistic"),
//        array('id' => 0,'guard_name' => 'api','name' => "Delete Logistic"),
//        array('id' => 0,'guard_name' => 'api','name' => "View Logistic Service"),
//        array('id' => 0,'guard_name' => 'api','name' => "Create Logistic Service"),
//        array('id' => 0,'guard_name' => 'api','name' => "Edit Logistic Service"),
//        array('id' => 0,'guard_name' => 'api','name' => "Delete Logistic Service"),
		);
		DB::table('permissions')->insert($permissions);
	}
}
