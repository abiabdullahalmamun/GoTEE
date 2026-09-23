<?php

namespace App\Services\Auth;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\ItemBrands\ItemBrandResource;
use App\Http\Resources\HkProdCategories\HkProdCategoryResource;
use App\Http\Resources\Items\ItemResource;
use App\Http\Resources\Suppliers\SupplierResource;
use App\Http\Resources\Uoms\UomResource;
use App\Http\Resources\Users\UserResource;
use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\CustomerRepositoryInterface;
use App\Interfaces\ItemBrandRepositoryInterface;
use App\Interfaces\HkProdCategoryRepositoryInterface;
use App\Interfaces\ItemRepositoryInterface;
use App\Interfaces\SupplierRepositoryInterface;
use App\Interfaces\UomRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected AuthRepositoryInterface $authRepository;
    protected UserRepositoryInterface $userRepository;
    protected ItemBrandRepositoryInterface $brandRepository;
    protected UomRepositoryInterface $uomRepository;
    protected HkProdCategoryRepositoryInterface $categoryRepository;

    protected ItemRepositoryInterface $itemRepository;
    protected SupplierRepositoryInterface $supplierRepository;
    protected CustomerRepositoryInterface $customerRepository;

    public function __construct
    (
        UserRepositoryInterface           $userRepository,
        AuthRepositoryInterface           $authRepository,
        ItemBrandRepositoryInterface      $brandRepository,
        UomRepositoryInterface            $uomRepository,
        HkProdCategoryRepositoryInterface $categoryRepository,
        ItemRepositoryInterface           $itemRepository,
        SupplierRepositoryInterface       $supplierRepository,
        CustomerRepositoryInterface       $customerRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->authRepository = $authRepository;
        $this->brandRepository = $brandRepository;
        $this->uomRepository = $uomRepository;
        $this->categoryRepository = $categoryRepository;
        $this->itemRepository = $itemRepository;
        $this->supplierRepository = $supplierRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @throws ExceptionHandler
     */
    public function login($data): array
    {
        date_default_timezone_set('Asia/Dhaka');
        try {
            $user = $this->authRepository->findByEmail($data['email']);

            if (!$user || !Hash::check($data['password'], $user->password)) {
                throw new ExceptionHandler('The provided credentials are incorrect.',ResponseStatus::CODE_UNAUTHORIZED);
            }
            $token = $this->authRepository->createToken($user);
            $brands = $this->brandRepository->getAll(status: true);
            $categories = $this->categoryRepository->getAll(status: true);
            $items = $this->itemRepository->getAll(status: true);
            $suppliers = $this->supplierRepository->getAll(status: true);
            $customers = $this->customerRepository->getAll(status: true);
            $uoms = $this->uomRepository->getAll(status: true);
            return [
                'token' => $token,
                'authData' => userResource::make($user),
                'uoms'=>UomResource::collection($uoms),
                'items'=>ItemResource::collection($items),
                'categories'=>HkProdCategoryResource::collection($categories),
                'brands'=>ItemBrandResource::collection($brands),
                'suppliers'=>SupplierResource::collection($suppliers),
                'customers'=>SupplierResource::collection($customers),
                'serverCurDate'=>Date('Y-m-d'),
            ];
        }
        catch (QueryException  $e) {
            throw new ExceptionHandler('Something went wrong.',ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }
}
