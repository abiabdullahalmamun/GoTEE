@extends('layouts.app')
@push('style')
@endpush
@section('content')

<style> 
  table.dataTable > thead > tr > th, table.dataTable > tbody > tr > td { 
    padding: 1px 3px !important; 
  } 
  select, input, button{
	  padding: 2px 5px !important;
  }
</style>

    <div class="mx-auto">
        <x-page-header title="Query Builder" />
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif

         @if ($errors->any())
		    <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg">
		        <ul class="list-disc pl-5">
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@endif
     
      <div class="bg-white shadow rounded p-2 mt-1 w-full">
           <form method="GET" action="{{ route('query-builder.index') }}" class="w-full">
 
  
<!-- <div class="flex justify-between items-center gap-3"> -->
<div class="flex flex-wrap gap-3 items-end">
    <!-- Input on Left -->
<div class="flex flex-col">
    <label for="tableQ" class="text-gray-700 mb-1 text-sm">
        <i>Table:</i>
    </label>
    <select name="tableQ" id="tableQ" class="w-40 border text-sm rounded">
        <option value=""></option>
        @foreach($tables as $item)
            <option value="{{ $item->id }}">
                {{ $item->table_name }}
            </option>
        @endforeach
    </select>
</div>
 
<div id="filter-container" class="flex flex-col">

    <!-- <div class="filter-row flex items-end gap-3 mb-3 text-sm"> -->
       <div class="filter-row flex flex-wrap items-end gap-2 text-sm"> 
        <div class="flex flex-col">
            <label>Column</label>
            <select name="columnQ[]" class="columnQ w-24 border p-2 rounded text-sm">
                <option value=""></option>
            </select>
        </div>

        <div class="flex flex-col">
            <label>Condition</label>
            <select name="conditionQ[]" class="w-24 border p-2 rounded text-sm">
                <option value="=">=</option>
                <option value=">">></option>
                <option value="<"><</option>
                <option value=">=">>=</option>
                <option value="<="><=</option>
                <option value="!=">!=</option>
                <option value="LIKE">LIKE</option>
                <option value="IN">IN</option>
                <option value="IS NULL">IS NULL</option>
                <option value="NOT LIKE">NOT LIKE</option>
                <option value="NOT IN">NOT IN</option>
                <option value="IS NOT NULL">IS NOT NULL</option>
            </select>
        </div>

        <div class="flex flex-col">
            <label>Value</label>
            <input type="text"
                   name="valueQ[]"
                   class="w-24 border rounded p-2 text-sm">
        </div>

        <button type="button"
                class="add-row flex-shrink-0  bg-green-500 text-white px-2 py-1 rounded">
            +
        </button>

        <button type="button" 
        class="remove-row flex-shrink-0 px-2 py-1 bg-red-500 text-white rounded">
            -
        </button>

    </div>

</div>
<div class="flex flex-col">
     <label for="total" class="text-gray-700 text-sm"><i>Sort Column:</i></label>
      <select name="sortQ" id="sortQ" class="w-40 border p-2 rounded text-sm">
                    <option value=""></option>
      </select>
</div>
 <div class="flex flex-col">
         <label for="total" class="text-gray-700 text-sm"><i>Sort:</i></label>
          <select name="orderQ" id="orderQ" class="w-24 border p-2 rounded text-sm">
                    <option value="ASC">ASC</option>
                     <option value="DESC">DESC</option>
        </select>
</div>
<div class="flex flex-col">
     <label for="total" class="text-gray-700 text-sm"><i>Limit:</i></label>
     <input type="number"
   name="limitQ"
   id="limitQ"
   value="10"
   class="w-24 text-sm border px-2 py-1.5 rounded">
 </div>
    <!-- Label on Right -->
  
          <div class="text-right ">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">SHOW</button>
     
        </div>
</div>

        <!-- <div class="mb-3 text-right"> -->



</form> 

    </div>

<div class="bg-white shadow rounded p-2 mt-1 w-full">

    @if(!empty($data) && count($data))

        <div class="overflow-x-auto overflow-y-auto max-h-[70vh] border rounded">
                <table id="reportTable" class="table-auto w-full" style="font-size:12px">
            <!-- <table  id="reportTable" class="min-w-full border-collapse"> -->

                <thead class="bg-gray-100 sticky top-0">
                    <tr>
                        @foreach(array_keys((array)$data[0]) as $col)
                            <th class="border p-1 whitespace-nowrap">
                                {{ $col }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach($data as $row)
                        <tr>
                            @foreach((array)$row as $value)
                                <td class="border p-0.5 whitespace-nowrap">
                                    {{ $value }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    @endif

</div>

</div>

@endsection

@push('scripts')
<script>
const tableColumns = @json($schema);
</script>

  
@endpush

@push('scripts')
    @vite(['resources/js/scripts/dbquery.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush
