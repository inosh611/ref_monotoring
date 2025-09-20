<?php
namespace Modules\Dealers\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Dealers\Repositories\Interfaces\OwnerRepositoryInterface;
use Modules\Dealers\Repositories\OwnerRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
    
        $this->app->bind(OwnerRepositoryInterface::class, OwnerRepository::class);   
        

    }

    public function boot()
    {
        
    }
}