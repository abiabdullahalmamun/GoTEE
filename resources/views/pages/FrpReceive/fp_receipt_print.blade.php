<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Receipt Print</title>
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
				Indian Visa Application Center
			</td>
		</tr>
		
		<tr>
			<td class="cName" colspan="2">
				{{$datas->center->center_name ?? 'N/A'}}
			</td>
		</tr>
		
		<tr>
			<td class="pDate">
				{{now()->format('d-M-Y') }}
			</td>
			<td class="pTime">
				{{now()->format('h:i:s A')}}
			</td>
		</tr>
		
		<tr>
			<td class="cName" colspan="2">
				<sub>{{$datas->sticker->sticker}}</sub><b>{{$datas->stickerNo}}</b>
			</td>
		</tr>
		
		<tr>
			<td class="cName" colspan="2">
				<canvas id="code39{{$n}}" class="barCode"></canvas>
			</td>
		</tr>
		
		<tr>
			<td class="">
				User: {{ $datas->user?->name ?? 'N/A' }}
			</td>
			<td class="">
				Counter: {{$datas->cntNo}}
			</td>
		</tr>
		
		<tr>
			<td class="" colspan="2">
				Code: {{$code}}
			</td>
		</tr>
		
		<tr>
			<td class="" colspan="2">
				Name: {{$datas->ApplicantName}}
			</td>
		</tr>
		
		<tr>
			<td class="" colspan="2">
				Passport:{{$datas->passport}} ({{$datas->psQty}})
			</td>
		</tr>

		<tr>
			<td class="" colspan="2">
				Webfile:{{$datas->Webfile}}
			</td>
		</tr>
		<tr>
			<td class="" colspan="2">
				Nationality:{{$fdata->nationality}}  
			</td>
		</tr>
		<tr>
			<td class="" colspan="2">
				Date of Checking:{{$fdata->Date}}  
			</td>
		</tr>
		<tr>
			<td class="" colspan="2">
				VisaType:{{$datas->visa->visa_type}}
			</td>
		</tr>
		<tr>
			<td class="" colspan="2">
				Remarks: {{$fdata->v_duration->name}}/{{$fdata->entry->name}}/{{$datas->visa->visa_type}}
			</td>
		</tr>
		<tr>
			<td class="" colspan="2">
				Visa fee: {{$fdata->visa_fee}} Tk
			</td>
			<td class="" colspan="2">
				Fax trans. charge: {{$fdata->fax_trans_charge}} Tk
			</td>
		</tr>
		<tr>
			<td class="" colspan="2">
				icwf: {{$fdata->icwf}} Tk
			</td>
			<td class="" colspan="2">
	
				Visa app. charge:{{$fdata->visa_app_charge}} Tk
			</td>
		</tr>
		<tr>
			<td>
				<?php 
				if (isset($datas->txn)) {
				    echo 'Online Transaction: '.$datas->txn ; 
				}
			   ?>
			</td>
		</tr>
		<tr>
			<td class="" colspan="2">
				Total Amount: {{$fdata->total_amount}} Tk
			</td>
		</tr>
	
		
		<tr>
			<td class="" colspan="2">
				SMS To:{{$datas->contact}}
			</td>
		</tr>
		
		<tr>
			<td class="fee" colspan="2">
				Payment:{{ $datas->pmethod == 1 ? 'Online' : ($datas->pmethod == 2 ? 'Cash' : 'Waive') }}<br>
				 
				Biometric: {{ $datas->bioType == 2 ? 'Required' : 'Not Required' }}
			</td>
		</tr>
		
		<tr>
			<td class="" colspan="2">
				<b>Tentative Delivery Date:</b>
			</td>
		</tr>
		
		<tr>
			<td class="" colspan="2">
				{{$datas->tdd ?? 'N/A'}}   {{$datas->center->del_time ?? 'N/A'}}
			</td>
		</tr>
		
		<tr>
			<td class="info" colspan="2">
				{{$datas->center->info ?? 'N/A'}}
			</td>
		</tr>
		
		<tr>
			<td class="declaration" colspan="2">
				I do hereby authorise IVAC to send SMS on the above mobile number.
			</td>
		</tr>
		
		<tr>
			<td class="note" colspan="2">
				<i>For Delivery, please ensure that your application status is “Ready for Delivery” by tracking your application visit -- www.ivacbd.com</i>
			</td>
		</tr>
		
		<tr>
			<td class="hotline" colspan="2">
				<b>Hotline: {{$datas->center->hotline ?? 'N/A'}}</b>
			</td>
		</tr>
		
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
<!-- <script>
    window.print();
    window.location.href = "{{ route('app-receive.index') }}";
</script> -->
@vite(['resources/js/scripts/fpreceiptprint.js'])

</body>
</html>
