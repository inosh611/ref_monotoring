<?php
namespace Modules\Employee\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Employee\Repositories\Interfaces\EmployeeRepositoryInterface;
use Modules\Employee\Repositories\EmployeeRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
    
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);   
        

    }

    public function boot()
    {
        
    }
}