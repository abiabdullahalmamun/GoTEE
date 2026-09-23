@extends('layouts.app')

@section('content')
    <div class=" mx-auto">
        <!-- Header Bar -->
        <x-page-header title="Product Category" />
        <!-- Card Section with Animation -->
        <div class="relative bg-white p-4 mt-1 shadow rounded ">
            <hk-categories />
        </div>

    </div>

@endsection

