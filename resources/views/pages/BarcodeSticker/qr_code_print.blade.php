
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Sticker Print</title>
    <br><br><br>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #fff;
        }

        .print-info {
            padding: 5px 0 10px;
            font-size: 12px;
            font-weight: bold;
        }

.sticker-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 3mm;
}

.qr-sticker {
    width: 25mm;
    min-height: 35mm;
    padding: 2mm;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    text-align: center;
    border: 1px solid #ccc;
    page-break-inside: avoid;
    break-inside: avoid;
}
/*.qr-code {
    width: 1in;
    height: 1in;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}*/
.qr-code {
    width: 0.6in;
    height: 0.6in;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.qr-code svg {
    width: 100% !important;
    height: 100% !important;
    max-width: 100%;
    display: block;
}

.qr-details {
    width: 100%;
    margin-top: 1mm;
    font-size: 9px;
    font-weight: bold;
    line-height: 1.35;
    text-align: center;
    overflow-wrap: anywhere;
}   

        .qr-details div {
            margin-bottom: 1px;
        }

        @page {
            margin: 5mm;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .print-info {
                display: none;
            }

            .qr-sticker {
                border: none;
            }
        }
    </style>
</head>

<body>

    <div class="print-info">
        Printing IDs: {{ $startNo }} - {{ $endNo }}
        | Total: {{ count($ids) }}
    </div>

    <div class="sticker-container">

        @forelse ($ids as $id)

            <div class="qr-sticker">

                <div class="qr-code">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(200)
                        ->margin(1)
                        ->generate((string) $id) !!}
                </div>

                <div class="qr-details">
                <!--     <div>DEMO COMPANY LIMITED</div>
                     <div>Name: Mst. Ria Akter</div>
                     <div>ID: SW-44140</div> -->
                     
                    <div>Order: {{ $orderCode }}</div>
                    <div> {{ $OrderNo }}</div>
                    <!-- <div>Attribute: {{ $attr }}</div> -->
                    <!-- <div>Style: {{ $Style }}</div> -->
                    <!-- <div>Buyer: {{ $buyer }}</div> -->
                    <div>ID: {{ $id }}</div>
                </div>

            </div>

        @empty

            <p>No QR codes available for printing.</p>

        @endforelse

    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>

</body>
</html>
