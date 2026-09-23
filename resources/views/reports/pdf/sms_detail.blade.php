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
		<b>Center:</b>  {{ $cnt ? $cnt : 'All' }} &emsp;&emsp; <b>Type:</b>
		{{ [0 => 'OTP', 1 => 'Received At Center', 2 => 'Sent2HCI', 4 => 'Received From HCI',5 => 'Delivered'][$type] ?? 'All' }}
		
	</div>
    <table>
        <thead>
            <tr>
    			<th>#</th>
				<th>Center</th>
				<th>Date</th>
				<th>Type</th>

				<th>Webfile</th>
				<th>Contact</th>
				<th>Text</th>  
				<th>CreatedAt</th>    
				<th>TXN</th>  
				<th>Status</th>   
				<th>DelivertAt</th>     
            </tr>
        </thead>
		
        <tbody>
           @foreach($data as $index => $row)
			<tr>
			<td class="text-center">{{ $index+1 }}</td>
			<td class="text-center">{{ $row->center->center_name ?? '' }}</td>
					<td class="text-center">{{ $row->Date ?? '' }}</td>
					@php
					    $badges = [
				        0 => ['label' => 'OTP', 'class' => 'badge-success text-red-700'],
				        1 => ['label' => 'Receive', 'class' => 'badge-danger text-black-700'],
				        2 => ['label' => 'Sent2HCI', 'class' => 'badge-danger text-blue-700'],
				        3 => ['label' => 'RecFrmHCI', 'class' => 'badge-danger text-green-700'],
				        4 => ['label' => 'ReadyCenter', 'class' => 'badge-danger text-green-700'],
				        5 => ['label' => 'Delivery', 'class' => 'badge-danger text-blue-700'],
					    ];
					    $badge = $badges[$row->type] ?? ['label' => 'Unknown', 'class' => 'badge-secondary'];
					@endphp
					<td class="text-center">
					 <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
					</td>
					
					<td class="text-center">{{ $row->web->Webfile ?? '' }}</td>
					<td class="text-center">{{ $row->contact ?? '' }}</td>
					<td class="text-left">{{ $row->text ?? '' }}</td>
					<td class="text-center">{{ mb_substr($row->created_at, 0, 19)}}</td>
					
					<td class="text-center">{{ $row->txn ?? '' }}</td>
					<td class="text-center">{{ $row->Delivery ?? '' }}</td>
					<td class="text-center">{{ mb_substr($row->Delivery_time, 0, 19)}}</td>
			</tr>
			@endforeach
        </tbody>
    </table>
</body>
</html>
