<!DOCTYPE html>
<html>
<head>
    <title>Missing Sticker Report</title>
    <style>
        @page { size: A4 landscape; margin: 10px 15px; }
            body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 8px; 
        }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 0.5px solid #818181; padding: 4px 3px; white-space: nowrap; }
        th { background-color: #e7e7e7; text-align: center; }
        td { text-align: center; }
        td.missing { white-space: normal; text-align: left; }
        .reportHead { margin-bottom: 10px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="reportHead">
        <b>Missing Sticker Report</b><br>
        <b>Date:</b> {{$displayDate}}<br>
        <b>Center:</b> {{$centerName}}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Series</th>
                <th>Start</th>
                <th>End</th>
                <th>Total</th>
                <th>Missing Numbers</th>
            </tr>
        </thead>
<!--         <tbody>
            @foreach($resultArray as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->series ?? '-' }}</td>
                    <td>{{ $row->start ?? '-' }}</td>
                    <td>{{ $row->end ?? '-' }}</td>
                    <td>{{ $row->total ?? 0 }}</td>
                    <td class="missing">{{ $row->missing ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody> -->
    </table>
</body>
</html>
