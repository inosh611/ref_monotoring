<?php
namespace Modules\MyCollections\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\MyCollections\Repositories\CashRepository;
use Modules\MyCollections\Repositories\ChequeRepository;
use Modules\MyCollections\Repositories\Interfaces\CashRepositoryInterface;
use Modules\MyCollections\Repositories\Interfaces\ChequeRepositoryInterface;
use Modules\MyCollections\Repositories\Interfaces\PaymentRepositoryInterface;
use Modules\MyCollections\Repositories\PaymentRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
    
        $this->app->bind(CashRepositoryInterface::class, CashRepository::class);
        $this->app->bind(ChequeRepositoryInterface::class, ChequeRepository::class);   
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);   
        

    }

    public function boot()
    {
        
    }
}