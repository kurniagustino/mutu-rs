@php
    $bgClass = 'fi-simple-page fi-login-page';
@endphp

<div class="{{ $bgClass }}">
    <style>
        /* Reset Global */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Override Filament Layout */
        .fi-simple-page,
        .fi-login-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            min-height: 100vh !important;
            width: 100vw !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 2rem !important;
            overflow-y: auto !important;
        }

        /* Full Screen Gradient Background */
        .login-background {
            background: transparent !important;
            width: 100% !important;
            min-height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 2rem !important;
        }

        /* Card - Centered Properly */
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }

        /* ✨ DARK MODE: Card background */
        .dark .login-card {
            background: #1f2937;
        }

        /* Logo */
        .logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: block;
            border: 3px solid #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        /* Title */
        h1 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }

        /* ✨ DARK MODE: Title color */
        .dark h1 {
            color: #f9fafb !important;
        }

        .subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        /* ✨ DARK MODE: Subtitle color */
        .dark .subtitle {
            color: #9ca3af !important;
        }

        /* Form Spacing */
        .fi-fo-field-wrp {
            margin-bottom: 0.75rem !important;
        }

        /* Labels */
        label {
            display: block !important;
            font-weight: 600 !important;
            color: #374151 !important;
            margin-bottom: 0.4rem !important;
            font-size: 0.875rem !important;
        }

        /* ✨ DARK MODE: Label color */
        .dark label {
            color: #e5e7eb !important;
        }

        /* Input - White Background in Light Mode */
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100% !important;
            padding: 0.65rem 0.875rem !important;
            border: 2px solid #e5e7eb !important;
            border-radius: 8px !important;
            font-size: 0.95rem !important;
            color: #111827 !important;
            background: #ffffff !important;
            transition: border-color 0.2s, background-color 0.2s, color 0.2s !important;
        }

        /* ✨ DARK MODE: Input styling */
        .dark input[type="text"],
        .dark input[type="email"],
        .dark input[type="password"] {
            background: #374151 !important;
            border-color: #4b5563 !important;
            color: #f9fafb !important;
        }

        input:focus {
            outline: none !important;
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
            background: #ffffff !important;
        }

        /* ✨ DARK MODE: Input focus */
        .dark input:focus {
            background: #4b5563 !important;
            border-color: #818cf8 !important;
            color: #f9fafb !important;
        }

        /* Error State */
        input[aria-invalid="true"],
        .fi-input-wrp-error input {
            border-color: #ef4444 !important;
            background: #ffffff !important;
            color: #111827 !important;
        }

        /* ✨ DARK MODE: Error state */
        .dark input[aria-invalid="true"],
        .dark .fi-input-wrp-error input {
            border-color: #f87171 !important;
            background: #374151 !important;
            color: #f9fafb !important;
        }

        input::placeholder {
            color: #9ca3af !important;
        }

        /* ✨ DARK MODE: Placeholder */
        .dark input::placeholder {
            color: #6b7280 !important;
        }

        /* Error Message */
        .fi-fo-field-wrp-error-message {
            color: #ef4444 !important;
            font-size: 0.8rem !important;
            margin-top: 0.25rem !important;
        }

        /* ✨ DARK MODE: Error message */
        .dark .fi-fo-field-wrp-error-message {
            color: #fca5a5 !important;
        }

        /* Button */
        button[type="submit"],
        .fi-btn {
            width: 100% !important;
            padding: 0.75rem !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            font-size: 0.95rem !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            margin-top: 1rem !important;
            transition: transform 0.2s, box-shadow 0.2s !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.5rem !important;
        }

        button:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4) !important;
        }

        button:disabled {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%) !important;
            cursor: wait !important;
            transform: none !important;
        }

        /* Spinner */
        .spinner {
            display: none;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        button:disabled .spinner {
            display: inline-block;
        }



        /* Footer */
        .footer {
            text-align: center;
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        /* ✨ DARK MODE: Footer border */
        .dark .footer {
            border-top-color: #4b5563 !important;
        }

        .footer-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.2rem;
        }

        /* ✨ DARK MODE: Footer text */
        .dark .footer-text {
            color: #d1d5db !important;
        }

        .footer-copy {
            font-size: 0.7rem;
            color: #94a3b8;
        }

        /* ✨ DARK MODE: Footer copy */
        .dark .footer-copy {
            color: #9ca3af !important;
        }

        /* Responsive */
        @media (max-width: 768px) {

            .fi-simple-page,
            .fi-login-page,
            .login-background {
                padding: 1.5rem !important;
            }
        }

        @media (max-width: 480px) {

            .fi-simple-page,
            .fi-login-page,
            .login-background {
                padding: 1rem !important;
            }

            .login-card {
                padding: 1.75rem 1.5rem;
            }

            h1 {
                font-size: 1.2rem;
            }

            .logo {
                width: 65px;
                height: 65px;
            }
        }
    </style>

    <div class="login-background">
        <div class="login-card">
            <!-- Logo -->
            <img src="{{ asset('images/logo-rs.png') }}" alt="Logo" class="logo">

            <!-- Title -->
            <h1>Aplikasi Indikator Mutu<br>RS Bhayangkara Jambi</h1>
            <p class="subtitle">Masuk untuk melanjutkan</p>

            <!-- Form -->
            <form wire:submit="authenticate" id="form">
                {{ $this->form }}

                <button type="submit" id="btn">
                    <span class="spinner"></span>
                    <span>Masuk</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="footer">
                <div class="footer-text">Tim Akreditasi RS Bhayangkara Jambi</div>
                <div class="footer-copy">© {{ date('Y') }} - All Rights Reserved</div>
            </div>
        </div>
    </div>

    <x-filament-actions::modals />

    <script>
        document.getElementById('form').addEventListener('submit', function() {
            document.getElementById('btn').disabled = true;
        });

        document.addEventListener('livewire:init', () => {
            Livewire.hook('commit', ({
                fail
            }) => {
                fail(() => {
                    document.getElementById('btn').disabled = false;
                });
            });
        });
    </script>
</div>
