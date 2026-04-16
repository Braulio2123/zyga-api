<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .meta {
            margin-bottom: 14px;
            font-size: 10px;
            color: #4b5563;
        }

        .filters {
            margin-bottom: 14px;
            padding: 8px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
        }

        .filters strong {
            display: inline-block;
            margin-right: 6px;
        }

        .filters span {
            margin-right: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #111827;
            color: #ffffff;
            border: 1px solid #d1d5db;
            padding: 6px;
            font-size: 10px;
            text-align: left;
        }

        tbody td {
            border: 1px solid #d1d5db;
            padding: 6px;
            vertical-align: top;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .empty {
            margin-top: 20px;
            padding: 12px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>

    <div class="meta">
        Generado: {{ $generatedAt }}
    </div>

    @php
        $visibleFilters = collect($filters)->filter(fn ($value) => filled($value));
    @endphp

    @if($visibleFilters->isNotEmpty())
        <div class="filters">
            <strong>Filtros aplicados:</strong>
            @foreach($visibleFilters as $key => $value)
                <span>{{ $key }} = {{ is_array($value) ? json_encode($value) : $value }}</span>
            @endforeach
        </div>
    @endif

    @if(empty($rows))
        <div class="empty">
            No hay información para exportar con los filtros indicados.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    @foreach($headings as $heading)
                        <th>{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>