<!DOCTYPE html>
<html>
<head>
    <title>Activity Summary Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10px 15px;
        }
        
		body { 
			font-family: DejaVu Sans, sans-serif; 
			font-size: 8px; 
		}
        
		table { 
			width: 100%; 
			border-collapse: collapse; 
		}
        
		th, td { 
			border: 0.5px solid #818181;
			padding: 2px 1px !important;
			white-space: nowrap; 
		}
        
		th { 
			background-color: #e7e7e7; 
		}
		
		.reportHead{
			margin-left: 100px;
			font-size: 10px;
		}
    </style>
</head>

<body>
	<div class="reportHead">
		<b>Activity Summary Report</b><br>
		<b>From:</b> {{$from}} <b>To:</b>  {{$to}}<br>
		<b>Center:</b>  {{ $cnt ? $cnt : 'All' }} 
	</div>
    <table>
        <thead>
            <tr>
               <th>#</th>
				<th class="p-2 border text-center">Center</th>
				<th class="p-2 border text-center">UserID</th>
				<th class="p-2 border text-center">User Name</th>
				<th class="p-2 border text-center">Total FormFill</th>
				<th class="p-2 border text-center">Total Receive</th>
				
				<th class="p-2 border text-center">Total ReadyCenter</th>
				<th class="p-2 border text-center">Total Delivery</th>
				<th class="p-2 border text-center">Total Bio</th>
				<th class="p-2 border text-center">Total</th>
            </tr>
        </thead>
		
        <tbody>
           @foreach($data as $index => $row)
			<tr>
			  <td class="p-2 border text-center">{{ $index + 1 }}</td>
			  <td class="p-2 border text-center">{{ $row->user->center->center_name ?? '' }}</td>
			  <td class="p-2 border text-center">{{ $row->user->name ?? '' }}</td>
			  <td class="p-2 border text-center">{{ $row->user->FullName ?? '' }}</td>
			    <td class="p-2 border text-center">{{ $row->total_forms ?? '' }}</td>
			  <td class="p-2 border text-center">{{ $row->total_rec ?? '' }}</td>
			  <td class="p-2 border text-center">{{ $row->total_ready ?? '' }}</td>
			  <td class="p-2 border text-center">{{ $row->total_del ?? '' }}</td>
			    <td class="p-2 border text-center">{{ $row->total_bio ?? '' }}</td>
			  <td class="p-2 border text-center">{{ $row->total_all ?? '' }}</td>
			</tr>
			@endforeach
        </tbody>
		 <tfoot class="bg-gray-100 font-semibold">
		    <tr>
		      <td colspan="4" class="p-2 border text-right">Grand Total</td>
		        <td class="p-2 border text-center">{{ $net_forms }}</td>
		      <td class="p-2 border text-center">{{ $net_rec }}</td>
		      <td class="p-2 border text-center">{{ $net_ready }}</td>
		      <td class="p-2 border text-center">{{ $net_del }}</td>
		        <td class="p-2 border text-center">{{ $net_del }}</td>
		      <td class="p-2 border text-center"></td>
		    </tr>
		  </tfoot>
    </table>
</body>
</html>
