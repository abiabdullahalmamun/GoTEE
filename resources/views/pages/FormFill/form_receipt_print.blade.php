<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Recepit Print</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	
	<link rel="stylesheet" href="public/assets/css/style.css">

    <style type="text/css" media="print">
        
        a[href]:after { 
			content: none !important; 
		}
        
		@page { 
			size: auto; 
			margin: 0px !important; 
		}
        
		html, body { 
			margin: 0px !important; 
			padding: 0px !important; 
			font-size: 13px;
			font-family: Courier New, sans-serif;
		}
        
		footer, header { 
			display: none; 
		}
		
		.banner{
			font-size: 14px;
			text-align: center;
		}
		
		.cName{
			text-align: center;
			font-size: 17px;
		}
		
		.pDate{
			border-bottom: 0.5px solid black;
		}
		
		.pTime{
			text-align: right;
			border-bottom: 0.5px solid black;
		}
		
		.barCode{
			width: 170px; 
			height: 55px;
		}
		
		.fee{
			font-size: 12px;
		}
		
		.info{
			text-align: center;
		}
		
		.declaration{
			text-align: justify;
			font-size: 11px;
		}
		
		.note{
			text-align: center;
			font-size: 11px;
		}
		
		.hotline{
			border-bottom: 1px solid black;
			text-align: center; 
		}
		
    </style>
</head>
<body>

    @php
    $n =1 ; 
    $barcodeData = [];
    
    $barcodeData[] = ['id' => 1, 'code' => $datas->Webfile];
    @endphp
	
	<table width=100%>
		
		<tr>
			<td class="banner" colspan="2">
			<b>	Indian Visa Application Center </b>
			</td>
		</tr>
		<tr>
			<td class="banner" colspan="2">
			<b>	Form Fill-in Receipt </b>
			</td>
		</tr>
		<tr>
			<td class="cName" colspan="2">
			<b>	{{$datas->center->center_name ?? 'N/A'}} </b>
			</td>
		</tr>
		
		<tr>
			<td class="pDate">
			<b>	{{now()->format('d-M-Y') }} </b>
			</td>
			<td class="pTime">
			<b>	{{now()->format('h:i:s A')}} </b>
			</td>
		</tr>
		
 
		
 
		
		<tr>
			<td class="">
			<b>	User: {{ $datas->user?->name ?? 'N/A' }} </b>
			</td>
			<td class="">
				<!-- Counter: {{$datas->cntNo}} -->
			</td>
		</tr>
		
 		<tr>
			<td class="" colspan="2">
			<b>	SL: {{$datas->day_sl}} </b>
			</td>
		</tr>
		
		<tr>
			<td class="" colspan="2">
			<b>	Name: {{$datas->Name}} </b>
			</td>
		</tr>
		
		<tr>
			<td class="" colspan="2">
			<b>	Passport: {{$datas->passport}}  </b>
			</td>
		</tr>
		
		<tr>
			<td class="" colspan="2">
			<b>	Webfile: {{$datas->webfile}} </b>
			</td>
		</tr>
		
	 
		
		<tr>
			<td class="" colspan="2">
			<b>	Contact: {{$datas->contact}} </b>
			</td>
		</tr>
<!-- 				<tr>
			<td class="" colspan="2">
			<b>	Remarks: {{$datas->remarks}} </b>
			</td>
		</tr> -->
		<tr>
			<td class="" colspan="2">
			<b>	Fee: {{$datas->fee}} BDT </b>
			</td>
		</tr>
 
 
<!-- 		<tr>
			<td class="declaration" colspan="2">
				I do hereby authorise IVAC to send SMS on the above mobile number.
			</td>
		</tr>
		 -->
		<!-- <tr>
			<td class="note" colspan="2">
				<i>For Delivery, please ensure that your application status is “Ready for Delivery” by tracking your application visit -- www.ivacbd.com</i>
			</td>
		</tr>
		 -->
		<!-- <tr>
			<td class="hotline" colspan="2">
				<b>Hotline: {{$datas->center->hotline ?? 'N/A'}}</b>
			</td>
		</tr>
		 -->
		<tr>
			<td class="" colspan="2">
				EasyQ
			</td>
		</tr>
		
	</table>


<script>
    window.barcodeData = @json($barcodeData);
    // console.log("Barcode data:", window.barcodeData);
</script>
<script>
    window.print();
    window.location.href = "{{ route('form-fill.index') }}";
</script>
@vite(['resources/js/scripts/receiptprint.js'])

</body>
</html>
