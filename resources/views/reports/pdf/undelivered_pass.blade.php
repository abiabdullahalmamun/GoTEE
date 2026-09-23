<!DOCTYPE html>
<html>
<head>
    <title>Undelivered Passport</title>
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
		<b>Undelivered Passport List</b><br>
		<b>Center:</b>  {{ $centerName ? $centerName : 'All' }} &emsp;&emsp; 
	</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
				<th>Date</th>
                <th>Passport</th>
                <th>Webfile</th>
                <th>Name</th>
                <th>Contact</th>
              	<th>stickerType</th>
              	<th>stickerNo</th>
              	<th>visatype</th>
                <th>Source</th>
              
              
            </tr>
        </thead>
		
        <tbody>
            @foreach($records as $index => $row)
            <tr>
                <td  class="text-center">{{ $index+1 }}</td>
               	<td class="text-center">{{ $row['date'] ?? '' }}</td>
	 				<td class="text-center">{{ $row['passport'] ?? '' }}</td>
					<td class="text-center">{{ $row['Webfile'] ?? '' }}</td>
	 				
	 				<td class="text-left">{{ $row['Name'] ?? '' }}</td>
				 	<td class="text-center">{{ $row['Contact'] ?? '' }}</td>
				 	<td class="text-center">{{ $row['sticker'] ?? '' }}</td>
				 	<td class="text-center">{{ $row['stickerNo'] ?? '' }}</td>
				 	<td class="text-center">{{ $row['visa'] ?? '' }}</td>
					<td class="text-left">{{ $row['source'] ?? '' }}</td>
				 
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
