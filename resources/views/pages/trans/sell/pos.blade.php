@extends('layouts.app')

@section('content')
    <div class=" mx-auto">
        <!-- Header Bar -->
        <x-page-header title="POS" />
        <!-- Card Section with Animation -->
        <div class="relative  mt-1 shadow rounded ">
            <pos-selling />
        </div>

    </div>

@endsection

