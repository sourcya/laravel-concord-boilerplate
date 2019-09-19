<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRolesModelRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        Schema::create('user_roles_model_records', function (Blueprint $table) use ($tableNames){
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('role_id');
            $table->unsignedBigInteger('model_id');
            $table->string('model_type');

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->timestamps();

            $table->primary(['user_id', 'role_id', 'model_id', 'model_type'],'prmKey');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_roles_model_records');
    }
}
