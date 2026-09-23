<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sticker Barcode Print</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style type="text/css" media="print">
        .barcode_jk {
            float: left;
            text-align: left;
            width: 80%;
            height: auto;
            margin: 0 auto 10px;
        }
        .barcode_jk p {
            text-align: center;
            width: 100%;
            margin: 3px 8px;
        }
        a[href]:after { content: none !important; }
        @page { size: auto; margin: 0mm; }
        html, body { margin: 0 !important; padding: 0 !important; }
        footer, header { display: none; }
    </style>
</head>
<body>

@php
    $n = 0;
    $barcodeData = [];
@endphp

@for ($i = $startNo; $i <= $endNo; $i++)
    @php
        $onlyBarcode = $barcode . $i;
        $barcodeData[] = ['id' => $n, 'code' => $onlyBarcode];
    @endphp

    <div class="barcode_jk">
        <div class="barcode_jk_sub">
            <p>
                <canvas id="code39{{ $n }}" style="width:170px; height:55px;"></canvas>
            </p>
        </div>
    </div>

    @php $n++; @endphp
@endfor

<script>
    window.barcodeData = @json($barcodeData);
    // console.log("Barcode data:", window.barcodeData);
</script>

@vite(['resources/js/scripts/barcodeprint.js'])

</body>
</html>
