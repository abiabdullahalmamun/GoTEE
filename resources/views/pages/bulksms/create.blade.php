@extends('layouts.app')

@section('content')
    <div class="mx-auto ">
        <!-- Page Header Component -->
        <div class=" max-w-full mx-auto">
            <x-page-header title="Bulk SMS" />
        </div>
        <!-- User Create Form -->
        <div class="bg-white shadow rounded p-6 mt-1 max-w-full mx-auto">
            <bulk-sms/>
        </div>
    </div>
@endsection
