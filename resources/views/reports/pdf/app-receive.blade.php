<!DOCTYPE html>
<html>
<head>
    <title>App Receive Report</title>
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
		<b>App Receive Report</b><br>
		<b>From:</b> {{$from}} <b>To:</b>  {{$to}}<br>
		<b>Center:</b>  {{ $cnt ? $cnt : 'All' }} &emsp;&emsp; <b>User:</b> {{ $usr ? $usr : 'All' }}  &emsp;&emsp;    <b>VisaType:</b>  {{ $vst ? $vst : 'All' }} &emsp;&emsp; <b>StickerType:</b> {{ $stc ? $stc : 'All' }}
	</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
				<th>Center</th>
                <th>Date</th>
                <th>Webfile</th>
                <th>Name</th>
                <th>Passport</th>
                <th>Sticker<br>Type</th>
                <th>StickerNo</th>
                <th>Contact</th>
                <th>Visatype</th>
                <th>Pay</th>
                <th>Transaction ID</th>
                <th>Remarks</th>
                <th>Corr</th>  
                <th>Pass<br>Qty</th>
                <th>Cnt</th>
                <th>Tkn</th>  
                <th>Received<br>By</th>  
                <th>ReceivedAt</th>   
            </tr>
        </thead>
		
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index+1 }}</td>
                <td>{{ $row->center->center_name ?? '' }}</td>
				<td>{{ $row->Date ?? '' }}</td>
				<td>{{ $row->Webfile ?? '' }}</td>
				<td>{{ $row->ApplicantName ?? '' }}</td>
				<td>{{ $row->passport ?? '' }}</td>
				<td>{{ $row->sticker->sticker ?? '' }}</td>
				<td>{{ $row->stickerNo ?? '' }}</td>
				<td>{{ $row->contact ?? '' }}</td>
				<td>{{ $row->visa->visa_type ?? '' }}</td>
				<td> 
					@if ($row->pmethod == 1)
					<span class="badge badge-success text-red-700">Online</span>
					@elseif ($row->pmethod == 2)
					<span class="badge badge-danger text-green-700">Cash</span>
					@elseif ($row->pmethod == 3)
					<span class="badge badge-danger text-blue-700">Waive</span>
					@endif
				</td>
				<td>{{ $row->txn ?? '' }}</td>
				<td>{{ $row->remarks ?? '' }}</td>
				<td>{{ $row->corrFee ?? '' }}</td>
				<td>{{ $row->psQty ?? '' }}</td>
				<td>{{ $row->cntNo ?? '' }}</td>
				<td>{{ $row->tknNo ?? '' }}</td>
				<td>{{ explode('@', $row->user->email ?? '')[0] }}</td>
				<td>{{ mb_substr($row->created_at, 0, 16)}}</td>
            </tr>
            @endforeach
        </tbody>
        <tr>
				    <td colspan="100%" style="height: 15px;"></td>
				</tr>
				<tfoot>
					 @foreach($paymentSummary as $type => $count)
					    <tr>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>

			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td class="border px-2 py-1">
			                	@if($type == 1)
							        Total Online
							    @elseif($type == 2)
							        Total Cash
							    @elseif($type == 3)
							        Total Waive
							    @else
							        N/A
							    @endif
			                </td>
			                <td class="border px-2 py-1 text-right">{{ $count }}</td>
			                
			            	<td></td>
			            	<td></td>
			            	
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            </tr>
			        @endforeach
			        <tr class="font-bold">
			        	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	
			            	<td></td>
			            	<td></td>
			            	<td></td>
			        	<td></td>
			            	<td></td>
			            	
			            <td class="border px-2 py-1">Grand Total</td>
			            <td class="border px-2 py-1 text-right">{{$paymentSummary->sum()}}</td>
			            <td></td>
			             <td class="border px-2 py-1">Total Correction</td>
			                <td class="border px-2 py-1 text-right">{{ $corrTotal }} BDT</td>
			              	<td></td>
			            	
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			        </tr>
				</tfoot>
    </table>
</body>
</html>
