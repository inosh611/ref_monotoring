<?php
namespace Modules\Target\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Target\Repositories\Interfaces\TargetRepositoryInterface;
use Modules\Target\Repositories\TargetRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
    
        $this->app->bind(TargetRepositoryInterface::class, TargetRepository::class);

    }

    public function boot()
    {
        
    }
}