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
        <td class="text-center title">DAILY FOREIGN PASSPORT SENDING STATUS</td>
    </tr>
    <tr>
        <td class="text-center">INDIAN VISA APPLICATION CENTER   Date: {{$formattedDate}}</td>
    </tr>
	<tr class="spacer"><td></td></tr>
</table>

<table class="amount-table">
	<tr>
		<td class="text-left">#</td>
		<td class="text-left">Name</td>
		<td class="text-left">Passport</td>
		<td class="text-left">Nationality</td>
		<td class="text-left">Date of <br>Receiving</td>
		<td class="text-left">StickerNo</td>
		<td class="text-left">ReceiptNo</td>
		<td class="text-left">Date of <br>Checking</td>
		<td class="text-left">Contact</td>
		<td class="text-left">Remarks</td>
	</tr>

	@foreach($data as $index => $row)
	<tr>
		<td>{{ $data->firstItem() + $index }}</td>
		<td>{{ $row->webref->ApplicantName ?? '' }}</td>
		<td>{{ $row->webref->passport ?? '' }}</td>
		<td>{{ $row->nationality ?? '' }}</td>
		<td>
			<?php
				$formatted = date('d-m-Y', strtotime($row->Date));
				echo $formatted; 
			?>
		</td>
		<td>
			<?php
			$stc = substr($row->webref->stickerNo, 9);
			$result = preg_replace('/([A-Za-z]+)(\d+)/', '$1-$2', $stc);
			echo $result ; 
			?>
		</td>
		<td> {{ $row->ReceiptNo ?? '' }}</td>
		<td>
			 <?php
				$formatted = date('d-m-Y', strtotime($row->Date));
				echo $formatted; 
			?>
		</td>
		<td>{{ $row->webref->contact ?? '' }}</td>
		<td>{{ $row->v_duration?->name ?? '-' }}/{{ $row->entry?->name ?? '-' }}/{{ $row->webref->visa->visa_type ?? '-'}}</td> 
	</tr>
	@endforeach
</table>

</body>
</html>
