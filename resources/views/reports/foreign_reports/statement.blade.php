<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<style media="print">
    @page {
        size: A4 landscape;
        margin: 20mm;
    }
</style>

<style>
    body {
        font-family: sans-serif, serif;
        font-size: 12px;
        line-height: 1.6;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .align-top { vertical-align: top; }

    .title {
        font-size: 20px;
    }

    .amount-table td {
        border: 1px solid #000;
        padding: 6px;
    }

    .spacer {
        height: 20px;
    }
</style>
</head>

<body>

<table>
    <!-- Header -->
    <tr>
        <td class="text-center title">
            INDIAN VISA APPLICATION CENTER
        </td>
    </tr>


    <tr>
        <td class="text-center">
           STATEMENT OF VISA FEE COLLECTION  {{$formattedDate}} <sub>TODAY USED RECEIPT ({{$bookNo}}) {{$recMin}} - {{$recMax}}</sub>
        </td>
    </tr>

    <tr class="spacer"><td></td></tr>

    <!-- Amount Table -->
    <tr>
        <td>
			<table class="amount-table">
				<tr>
					<td class="text-left"><strong>#</strong></td>
					<td class="text-left"><strong>Name</strong></td>
					<td class="text-left"><strong>Passport</strong></td>
					<td class="text-left"><strong>Nationality</strong></td>
					<td class="text-left"><strong>ReceiptNo</strong></td>
					<td class="text-left"><strong>Visa Fee</strong></td>
					<td class="text-left"><strong>Fax Trans. <br>Charge</strong></td>
					<td class="text-left"><strong>ICWF</strong></td>
					<td class="text-left"><strong>Visa App. <br>Charge</strong></td>
					<td class="text-left"><strong>Total Amount</strong></td>
					<td class="text-left"><strong>Remarks</strong></td>
				</tr>
				
				@foreach($data as $index => $row)
				<tr>
					<td>{{ $data->firstItem() + $index }}</td>
					<td>{{ $row->webref->ApplicantName ?? '' }}</td>
					<td>{{ $row->webref->passport ?? '' }}</td>
					<td>{{ $row->nationality ?? '' }}</td>
					<td> {{ $row->ReceiptNo ?? '' }}</td>
					<td class="text-right"> {{ $row->visa_fee ?? '' }}</td>
					<td class="text-right"> {{ $row->fax_trans_charge ?? '' }}</td>
					<td class="text-right"> {{ $row->icwf ?? '' }}</td>
					<td class="text-right"> {{ $row->visa_app_charge ?? '' }}</td>
					<td class="text-right"> {{ $row->total_amount ?? '' }}</td>
					<td>{{ $row->v_duration?->name ?? '-' }}/{{ $row->entry?->name ?? '-' }}/{{ $row->webref->visa->visa_type ?? '-'}}</td> 
				</tr>
				@endforeach
					
				<tr>
					<td colspan="5" class="text-right"><strong>Total =</strong></td>
					<td class="text-right"><strong>{{ number_format((float)$totalBDTvisa, 2, '.', ',') }}</strong></td>
					<td class="text-right"><strong>{{ number_format((float)$totalBDTfax, 2, '.', ',') }}</strong></td>
					<td class="text-right"><strong>{{ number_format((float)$totalBDTicwf, 2, '.', ',') }}</strong></td>
					<td class="text-right"><strong>{{ number_format((float)$totalBDTvisaapp, 2, '.', ',') }}</strong></td>
					<td class="text-right"><strong>{{ number_format((float)$totalAllBDT, 2, '.', ',') }}</strong></td>
					<td></td>
				</tr>
            </table>
        </td>
    </tr>
</table>

<table>
    <tr class="spacer"><td></td></tr>
    <tr class="spacer"><td></td></tr>
	<tr class="spacer"><td></td></tr>

    <!-- Signature -->
    <tr>
		<td class="text-left">
			<strong>Amount in words: 
			<?php 
                $digit = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                $words = ucwords($digit->format(round($totalAllBDT)));
                echo   $words ;
            ?>
			<br>
			Visa Section, HCI, and Accounts Section, HCI.</strong>
        </td>
        <td class="text-right">
            <strong>Dy. COO / Incharge (Operation)<br>
            Indian Visa Application Center</strong>
        </td>
    </tr>
</table>

</body>
</html>
