<?php

namespace App\Repositories;

use App\Interfaces\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class CustomerRepository implements CustomerRepositoryInterface
{
    use HasApiTokens;

    public function getAll(bool $status)
    {
        return Customer::get();
    }

    public function create($data) : ?Customer
    {
        return Customer::create($data);
    }

    public function getById(int $id) : ?Customer
    {
        return Customer::find($id);
    }
    public function getLastCustomer(int $authId)
    {
        return Customer::orderby('id', 'desc')->first();
    }

}
