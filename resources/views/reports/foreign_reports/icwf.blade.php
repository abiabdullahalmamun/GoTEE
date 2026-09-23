<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<style media="print">
    @page {
        size: A4;
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
        <td class="text-right"><strong>[Foreign=Wing]</strong></td>
    </tr>

    <tr>
        <td class="text-center title">
            INDIAN VISA APPLICATION CENTER
        </td>
    </tr>
	<tr class="spacer"><td></td></tr>

    <tr>
        <td class="text-right">
            Date: {{$formattedDate}}
        </td>
    </tr>

    <tr class="spacer"><td></td></tr>

    <!-- Address -->
    <tr>
        <td class="text-left">
            To<br>
            The Cashier<br>
            High Commission of India<br>
            Through: Visa Wing
        </td>
    </tr>

    <tr class="spacer"><td></td></tr>
	<tr class="spacer"><td></td></tr>

    <!-- Paragraph 1 -->
    <tr>
        <td class="text-left">
            <strong>
            Please receive from IVAC the sum of BD Taka
            <?php 
                $digit = new NumberFormatter('en', NumberFormatter::SPELLOUT);
				$words = ucwords($digit->format($totalBDTicwf));
				$fmt = new NumberFormatter('en_IN', NumberFormatter::DECIMAL);
				$number = $fmt->format($totalBDTicwf);
				echo $number . '/- (Taka ' . $words . ' Only)';
            ?>
            equivalent to Rs.
            <?php 
                     $val = round($totalRsicwf,2) ;
				$digit = new NumberFormatter('en', NumberFormatter::SPELLOUT);
				$words = ucwords($digit->format($val));
				$fmt = new NumberFormatter('en_IN', NumberFormatter::DECIMAL);
				$number = $fmt->format($val);
				echo $number . '/- (Rupees ' . $words . ' Only)';
            ?>
            </strong>
        </td>
    </tr>

    <tr class="spacer"><td></td></tr>
	<tr class="spacer"><td></td></tr>

    <!-- Paragraph 2 -->
    <tr>
        <td class="text-left">
			On account of Cash received from foreign passport holders towards their contribution to ICWF. The amount has been deposited in Mission's A/C No. 05420216820001
        </td>
    </tr>

    <tr class="spacer"><td></td></tr>
	<tr class="spacer"><td></td></tr>

    <!-- Amount Table -->
    <tr>
        <td>
            <table class="amount-table">
                <tr>
                    <td></td>
                    <td class="text-right">Taka Amount</td>
                    <td class="text-right">Rs. Amount</td>
                    <td class="text-right">Receipt No</td>
                </tr>

                <tr>
                    <td>1. ICWF</td>
                    <td class="text-right">{{ number_format($totalBDTicwf, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($totalRsicwf, 2, '.', ',') }} </td>
                    <td class="text-right">{{$bookNo}}</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr class="spacer"><td></td></tr>
    <tr class="spacer"><td></td></tr>
	<tr class="spacer"><td></td></tr>

    <!-- Signature -->
    <tr>
        <td class="text-right">
            Dy. COO / Incharge (Operation)<br>
            Indian Visa Application Center
        </td>
    </tr>

    <tr class="spacer"><td></td></tr>
	<tr class="spacer"><td></td></tr>

    <!-- Footer -->
    <tr>
        <td class="text-left">
            Credit Item No...................... Dated........................ Received by<br>
            Sum BDT Taka........................ Equivalent to Rs......................<br>
            The item no. under which the amount is shown in the main cash book may be given here.
        </td>
    </tr>
</table>

</body>
</html>
