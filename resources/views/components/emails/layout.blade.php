<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? '1310 Studio' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Georgia', serif;
            background-color: #f5f3f0;
            color: #1a1a1a;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
        }
        .header {
            background: #ffffff;
            padding: 40px 48px 32px;
            border-bottom: 1px solid #e8e4df;
            text-align: center;
        }
        .header img {
            height: 40px;
            width: auto;
        }
        .body {
            padding: 48px;
        }
        .footer {
            padding: 32px 48px;
            border-top: 1px solid #e8e4df;
            text-align: center;
        }
        h1 {
            font-family: 'Georgia', serif;
            font-size: 28px;
            font-weight: 400;
            font-style: italic;
            color: #1a1a1a;
            line-height: 1.3;
            margin-bottom: 16px;
        }
        h2 {
            font-family: 'Georgia', serif;
            font-size: 13px;
            font-weight: 400;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #927F64;
            margin-bottom: 24px;
        }
        p {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 14px;
            line-height: 1.7;
            color: #4a4a4a;
            margin-bottom: 16px;
            font-weight: 300;
        }
        .btn {
            display: inline-block;
            background: #1a1a1a;
            color: #ffffff !important;
            text-decoration: none;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 11px;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            padding: 14px 32px;
            margin: 24px 0;
        }
        .btn-outline {
            display: inline-block;
            border: 1px solid #1a1a1a;
            color: #1a1a1a !important;
            text-decoration: none;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 11px;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            padding: 14px 32px;
            margin: 24px 0;
        }
        .divider {
            border: none;
            border-top: 1px solid #e8e4df;
            margin: 32px 0;
        }
        .label {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 10px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #927F64;
            display: block;
            margin-bottom: 4px;
        }
        .value {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 400;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin: 24px 0;
        }
        .grid-item {}
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 12px 0;
            border-bottom: 1px solid #f0ede9;
        }
        .item-name {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 13px;
            color: #1a1a1a;
        }
        .item-qty {
            font-size: 12px;
            color: #927F64;
        }
        .item-price {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 13px;
            color: #1a1a1a;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .total-label {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 12px;
            color: #8a8a8a;
        }
        .total-value {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 12px;
            color: #1a1a1a;
        }
        .grand-total-label {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 10px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #8a8a8a;
        }
        .grand-total-value {
            font-family: 'Georgia', serif;
            font-size: 22px;
            color: #1a1a1a;
        }
        .badge {
            display: inline-block;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 10px;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            padding: 4px 10px;
            border: 1px solid #e8e4df;
            color: #4a4a4a;
        }
        .footer p {
            font-size: 11px;
            color: #b0a898;
            margin-bottom: 4px;
        }
        .footer a {
            color: #927F64;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .wrapper { margin: 0; }
            .body { padding: 32px 24px; }
            .header { padding: 28px 24px; }
            .footer { padding: 24px; }
            .grid { grid-template-columns: 1fr; gap: 16px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">

        {{-- Header --}}
        <div class="header">
            <img src="{{ asset('images/logo_menu.png') }}" alt="1310 Studio">
        </div>

        {{-- Body --}}
        <div class="body">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>1310 Studio · Luxury Flower Lab</p>
            <p>Orizaba 78, Roma Norte, Cuauhtémoc, CDMX 06700</p>
            <p>Lun–Vie 9:00–19:00 · Sáb 9:00–15:00</p>
            <p style="margin-top: 12px;">
                <a href="{{ url('/') }}">{{ url('/') }}</a>
            </p>
        </div>

    </div>
</body>
</html>