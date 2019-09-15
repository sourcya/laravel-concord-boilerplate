<?php

namespace Sourcya\BoilerplateBox\Commands;

use BoilerplatePermissionsTableSeeder;
use CorePermissionsTableSeeder;
use GeoSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Sourcya\UserModule\Models\UserProxy;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SourcyaInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sourcya:boilerplate-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installing Sourcya Boilerplate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!Schema::hasTable('user')) {
            try {
                $this->overrideKernel();
                $this->clearDefaultRoutes();
                $this->removeDefaultMigrationFiles();
                $this->overrideConfigFiles();
                Artisan::call('config:clear');
                Artisan::call('cache:clear');
                $this->info('Configuration Cache & Config Cache Cleared Successfully');
                Artisan::call('migrate:fresh');
                $this->info('Migration Files Migrated Successfully');
                Artisan::call('passport:install --force');
                $this->info('Passport has been Installed Successfully');
                Artisan::call('storage:link');
                $this->info('Storage has been Linked Successfully');
                Artisan::call('db:seed', [
                    '--class' => BoilerplatePermissionsTableSeeder::class
                ]);
                Artisan::call('db:seed', [
                    '--class' => CorePermissionsTableSeeder::class
                ]);
                Artisan::call('db:seed', [
                    '--class' => GeoSeeder::class
                ]);
                $this->info('Countries,States,Cities, and Permissions seeded Successfully');
            } catch (\Exception $e) {
                $this->error($e->getMessage() . '\n');
                $this->error("Installation Failed");
                return false;
            }

            $credentials = $this->createAdmin();
            $this->info('Admin User Credentials has been Generated Successfully');
            $this->table(['email','password'],array([$credentials[0],$credentials[1]]));
            $this->info('Successful Installation');
            return true;
        } else {
            $this->info('Sourcya Boilerplate already has been installed');
            return false;
        }
    }

    /**
     * @return bool
     */
    public function overrideKernel(){
        try{
            $defaultKernelPath = app_path()."/Http/Kernel.php";
            $newKernelPath = base_path()."/vendor/sourcya/boilerplate/src/ConfigFiles/Kernel.php";
            $defaultKernelFile = fopen($defaultKernelPath, 'w+');
            $newKernelContent = file_get_contents($newKernelPath);
            fwrite($defaultKernelFile, $newKernelContent);
            fclose($defaultKernelFile);
            $this->info('Default Laravel Kernel Overrode Successfully');
            return true;
        } catch (\Exception $e) {
            $this->error($e->getMessage() . '\n');
            $this->error("Failed To Override Default Laravel Kernel");
            return false;
        }
    }

    /**
     * @return bool
     */
    public function clearDefaultRoutes(){
        try{
            $defaultWebRoutePath = base_path()."/routes/web.php";
            $defaultApiRoutePath = base_path()."/routes/api.php";
            $defaultWebRouteFile = fopen($defaultWebRoutePath, 'w+');
            $defaultApiRouteFile = fopen($defaultApiRoutePath, 'w+');
            fclose($defaultWebRouteFile);
            fclose($defaultApiRouteFile);
            $this->info('Default Laravel Routes Files Cleared Successfully');
            return true;
        } catch (\Exception $e) {
            $this->error($e->getMessage() . '\n');
            $this->error("Failed To Clear Default Laravel Routes");
            return false;
        }
    }

    /**
     * @return bool
     */
    public function removeDefaultMigrationFiles(){
        try{
            $defaultMigrationFilesPath = base_path()."/database/migrations";
            $migrationFiles = glob($defaultMigrationFilesPath . '/*');
            foreach ($migrationFiles as $migrationFile){
                unlink($migrationFile);
            }
            $this->info('Default Laravel Migration Files Deleted Successfully');
            return true;
        } catch (\Exception $e) {
            $this->error($e->getMessage() . '\n');
            $this->error("Failed To Remove Default Laravel Migration Files");
            return false;
        }
    }

    /**
     * @return bool
     */
    public function overrideConfigFiles(){
        try{
            $defaultAuthConfigFilePath = base_path()."/config/auth.php";
            $defaultDatabaseConfigFilePath = base_path()."/config/database.php";
            $newAuthConfigFilePath = base_path()."/vendor/sourcya/boilerplate/src/ConfigFiles/auth.php";
            $newDatabaseConfigFilePath = base_path()."/vendor/sourcya/boilerplate/src/ConfigFiles/database.php";
            $defaultAuthConfigFile = fopen($defaultAuthConfigFilePath, 'w+');
            $defaultDatabaseConfigFile = fopen($defaultDatabaseConfigFilePath, 'w+');
            $newAuthConfigFileContent = file_get_contents($newAuthConfigFilePath);
            $newDatabaseConfigFileContent = file_get_contents($newDatabaseConfigFilePath);
            fwrite($defaultAuthConfigFile, $newAuthConfigFileContent);
            fwrite($defaultDatabaseConfigFile, $newDatabaseConfigFileContent);
            fclose($defaultAuthConfigFile);
            fclose($defaultDatabaseConfigFile);
            $this->info('Default Laravel (auth & database) Config Files Overrode Successfully');
            return true;
        } catch (\Exception $e) {
            $this->error($e->getMessage() . '\n');
            $this->error("Failed To Override Default Laravel Config Files");
            return false;
        }
    }
    /**
     * @return array
     */
    public function createAdmin()
    {
        $permissions = Permission::all();

        if (!Role::where('name', '=', 'Admin')->exists()) {
            $role = Role::create(['name' => 'Admin']);
            Role::create(['name' => 'Client']);
            Role::create(['name' => 'Agent']);
        }

        $role->syncPermissions($permissions);
        $email = 'admin@example.com';
        $password = str_random(8);
        $user = UserProxy::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $user->assignRole($role);
        return [$email,$password];
    }
}
