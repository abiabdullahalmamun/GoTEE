<?php

namespace App\Providers;

use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\CustomerRepositoryInterface;
use App\Interfaces\IssueItemsRepositoryInterface;
use App\Interfaces\IssueRepositoryInterface;
use App\Interfaces\IssueReturnRepositoryInterface;
use App\Interfaces\ItemAttributeRepositoryInterface;
use App\Interfaces\ItemBrandRepositoryInterface;
use App\Interfaces\HkProdCategoryRepositoryInterface;
use App\Interfaces\ItemRepositoryInterface;
use App\Interfaces\ItemSerialRepositoryInterface;
use App\Interfaces\ItemVariantRepositoryInterface;
use App\Interfaces\OrderItemsRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\StockItemSerialRepositoryInterface;
use App\Interfaces\ReceiveItemsRepositoryInterface;
use App\Interfaces\ReceiveRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ShiftRepositoryInterface;
use App\Interfaces\StockItemsRepositoryInterface;
use App\Interfaces\StockRepositoryInterface;
use App\Interfaces\StoreRepositoryInterface;
use App\Interfaces\SupplierRepositoryInterface;
use App\Interfaces\UomRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\AuthRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\IssueItemRepository;
use App\Repositories\IssueRepository;
use App\Repositories\IssueReturnRepository;
use App\Repositories\ItemAttributeRepository;
use App\Repositories\ItemBrandRepository;
use App\Repositories\HkProdCategoryRepository;
use App\Repositories\ItemRepository;
use App\Repositories\ItemSerialRepository;
use App\Repositories\ItemVariantRepository;
use App\Repositories\OrderItemsRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReceiveItemRepository;
use App\Repositories\StockItemSerialRepository;
use App\Repositories\ReceiveRepository;
use App\Repositories\ReportRepository;
use App\Repositories\ShiftRepository;
use App\Repositories\StockItemRepository;
use App\Repositories\StockRepository;
use App\Repositories\StoreRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\UomRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(HkProdCategoryRepositoryInterface::class, HkProdCategoryRepository::class);
        $this->app->bind(ItemBrandRepositoryInterface::class, ItemBrandRepository::class);
        $this->app->bind(UomRepositoryInterface::class, UomRepository::class);
        $this->app->bind(ItemRepositoryInterface::class, ItemRepository::class);
        $this->app->bind(ItemSerialRepositoryInterface::class, ItemSerialRepository::class);
        $this->app->bind(StoreRepositoryInterface::class, StoreRepository::class);
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(ItemVariantRepositoryInterface::class, ItemVariantRepository::class);
        $this->app->bind(ItemAttributeRepositoryInterface::class, ItemAttributeRepository::class);
        $this->app->bind(ShiftRepositoryInterface::class, ShiftRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderItemsRepositoryInterface::class, OrderItemsRepository::class);
        $this->app->bind(ReportRepositoryInterface:: class,ReportRepository::class);
        $this->app->bind(ReceiveRepositoryInterface:: class,ReceiveRepository::class);
        $this->app->bind(ReceiveItemsRepositoryInterface:: class,ReceiveItemRepository::class);
        $this->app->bind(IssueRepositoryInterface:: class,IssueRepository::class);
        $this->app->bind(IssueItemsRepositoryInterface:: class,IssueItemRepository::class);
        $this->app->bind(IssueReturnRepositoryInterface:: class,IssueReturnRepository::class);
        $this->app->bind(IssueItemsRepositoryInterface:: class,IssueItemRepository::class);
        $this->app->bind(StockRepositoryInterface:: class,StockRepository::class);
        $this->app->bind(StockItemsRepositoryInterface:: class,StockItemRepository::class);
        $this->app->bind(StockItemSerialRepositoryInterface:: class,StockItemSerialRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
