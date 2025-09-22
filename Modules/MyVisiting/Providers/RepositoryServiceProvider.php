<?php
namespace Modules\MyVisiting\Providers;

use Illuminate\Support\ServiceProvider;

use Modules\MyVisiting\Repositories\Interfaces\MyVisitingRepositoryInterface;
use Modules\MyVisiting\Repositories\MyVisitingRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
    
        $this->app->bind(MyVisitingRepositoryInterface::class, MyVisitingRepository::class);

    }

    public function boot()
    {
        
    }
}