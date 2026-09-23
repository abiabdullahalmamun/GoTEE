<!DOCTYPE html>
<html>
<head>
    <title>Appointment Override Report</title>
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
		<b>Appointment Override Report</b><br>
		<b>From:</b> {{$from}} <b>To:</b>  {{$to}}<br>
		<b>Center:</b>  {{ $cnt ? $cnt : 'All' }} &emsp;&emsp; <b>User:</b> {{ $usr ? $usr : 'All' }}
	</div>
    <table>
        <thead>
            <tr>
               <th>#</th>
				<th>Center</th>
				<th>Date</th>
				<th>Webfile</th>
				<th>VisaType</th>
				<th>Remarks</th>
				<th>CreatedBy</th>  
				<th>CreatedAt</th>    
				<th>Kiosk</th>  
				<th>CheckedAt</th>    
            </tr>
        </thead>
		
        <tbody>
           @foreach($data as $index => $row)
			<tr>
			<td class="text-center">{{ $index+1 }}</td>
			<td class="text-center">{{ $row->center->center_name ?? '' }}</td>
			<td class="text-center">{{ $row->Date ?? '' }}</td>
			<td class="text-center">{{ $row->WebFile_no ?? '' }}</td>
			<td class="text-center">{{ $row->visa->visa_type ?? '' }}</td>
			<td class="text-center">{{ $row->remarks ?? '' }}</td>
			<td class="text-center">{{ explode('@', $row->user->email ?? '')[0] }}</td>
			<td class="text-center">{{ mb_substr($row->created_at, 0, 19)}}</td>
			
			<td class="text-center">{{ $row->kioskId ?? '' }}</td>
			<td class="text-center">{{ mb_substr($row->checked_at, 0, 19)}}</td>
			</tr>
			@endforeach
        </tbody>
    </table>
</body>
</html>
