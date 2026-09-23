@extends('layouts.app')

@section('content')
    <div class=" mx-auto">
        <!-- Header Bar -->
        <x-page-header title="Product - Create" />
        <!-- Card Section with Animation -->
        <div class="relative bg-gray-50 mt-1 shadow rounded ">
            <product-create />
        </div>

    </div>

@endsection

