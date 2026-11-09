<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Arsip PT Sucofindo')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-teal: #1b6b63;
            --primary-teal-hover: #14554e;
            --soft-bg: #ece8f4;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--soft-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 0.75rem;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            max-width: 440px;
            width: 100%;
            padding: 2.5rem 2rem 1.75rem 2rem;
        }

        .btn-teal {
            background-color: var(--primary-teal);
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem;
            transition: all 0.2s ease-in-out;
        }

        .btn-teal:hover {
            background-color: var(--primary-teal-hover);
            color: #ffffff;
        }

        .text-teal {
            color: var(--primary-teal) !important;
        }

        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
        }

        .form-control:focus {
            border-color: var(--primary-teal);
            box-shadow: 0 0 0 0.25rem rgba(27, 107, 99, 0.15);
        }

        .input-icon-group {
            position: relative;
        }

        .input-icon-group i.field-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            z-index: 5;
        }

        .input-icon-group input {
            padding-left: 2.75rem;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            z-index: 5;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>