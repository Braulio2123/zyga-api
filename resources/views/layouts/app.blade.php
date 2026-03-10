<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Zyga PWA' }}</title>

    <meta name="theme-color" content="#0f172a">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f1f5f9;
            color: #0f172a;
        }

        .app-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
        }

        .subtitle {
            font-size: 14px;
            color: #475569;
            margin-bottom: 24px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .btn {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-danger {
            background: #dc2626;
            color: #fff;
        }

        .btn-secondary {
            background: #0f172a;
            color: #fff;
        }

        .message {
            margin-top: 16px;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            display: none;
        }

        .message.success {
            background: #dcfce7;
            color: #166534;
            display: block;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
            display: block;
        }

        .dashboard-card {
            width: 100%;
            max-width: 600px;
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-top: 16px;
            margin-bottom: 16px;
        }

        .actions {
            display: grid;
            gap: 12px;
            margin-top: 20px;
        }

        .muted {
            color: #64748b;
            font-size: 14px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>