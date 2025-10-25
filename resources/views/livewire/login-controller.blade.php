<div>
    @push('styles')
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html,
            body {
                height: 100%;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            }

            /* Animated Background with Floating Blobs */
            .animated-bg {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                overflow: hidden;
                background: linear-gradient(135deg, #fef3c7 0%, #fecaca 25%, #ddd6fe 50%, #bfdbfe 75%, #fed7aa 100%);
                z-index: 0;
            }

            /* Dark Mode Background */
            @media (prefers-color-scheme: dark) {
                .animated-bg {
                    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 25%, #1e3a8a 50%, #312e81 75%, #1e293b 100%);
                }

                .login-card {
                    background: rgba(30, 41, 59, 0.85) !important;
                    border: 1px solid rgba(148, 163, 184, 0.2) !important;
                }

                .app-title {
                    color: #f1f5f9 !important;
                }

                .app-subtitle,
                .footer-text {
                    color: #cbd5e1 !important;
                }

                .footer-copyright {
                    color: #94a3b8 !important;
                }

                label {
                    color: #e2e8f0 !important;
                }

                input {
                    background: rgba(15, 23, 42, 0.8) !important;
                    color: #f1f5f9 !important;
                    border-color: rgba(148, 163, 184, 0.4) !important;
                }

                input::placeholder {
                    color: #94a3b8 !important;
                }

                input:focus {
                    background: rgba(15, 23, 42, 0.9) !important;
                    border-color: #6366f1 !important;
                }
            }

            /* Animated Blob Shapes */
            .blob {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                opacity: 0.5;
                animation: float 20s ease-in-out infinite;
            }

            .blob-1 {
                width: 500px;
                height: 500px;
                background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
                top: -250px;
                left: -250px;
                animation-delay: 0s;
            }

            .blob-2 {
                width: 400px;
                height: 400px;
                background: linear-gradient(135deg, #f472b6 0%, #fb923c 100%);
                top: 50%;
                right: -200px;
                animation-delay: 2s;
            }

            .blob-3 {
                width: 600px;
                height: 600px;
                background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
                bottom: -300px;
                left: 50%;
                animation-delay: 4s;
            }

            @keyframes float {

                0%,
                100% {
                    transform: translate(0, 0) rotate(0deg);
                }

                33% {
                    transform: translate(50px, -50px) rotate(120deg);
                }

                66% {
                    transform: translate(-50px, 50px) rotate(240deg);
                }
            }

            /* Main Container */
            .login-container {
                position: relative;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
                z-index: 1;
            }

            /* Login Card - LEBIH LEBAR (LANDSCAPE) */
            .login-card {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                border-radius: 24px;
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
                padding: 2rem 4rem;
                width: 100%;
                max-width: 650px;
                position: relative;
                z-index: 10;
                animation: slideUp 0.8s ease-out;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(40px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Logo Section */
            .logo-section {
                text-align: center;
                margin-bottom: 1.5rem;
            }

            .logo-wrapper {
                display: inline-block;
                position: relative;
                margin-bottom: 1rem;
                animation: logoFloat 3s ease-in-out infinite;
            }

            @keyframes logoFloat {

                0%,
                100% {
                    transform: translateY(0px);
                }

                50% {
                    transform: translateY(-12px);
                }
            }

            .logo-wrapper img {
                width: 90px;
                height: 90px;
                border-radius: 50%;
                border: 4px solid rgba(255, 255, 255, 0.8);
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                object-fit: cover;
                transition: all 0.3s ease;
            }

            .logo-wrapper:hover img {
                transform: scale(1.05);
                box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
            }

            .app-title {
                font-size: 1.4rem;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 0.4rem;
                line-height: 1.3;
                letter-spacing: -0.02em;
            }

            .app-subtitle {
                font-size: 0.8rem;
                color: #64748b;
                font-weight: 500;
                margin-bottom: 0;
            }

            /* Form Styling - FIX TEXT COLOR */
            input[type="text"],
            input[type="email"],
            input[type="password"] {
                width: 100%;
                padding: 0.75rem 1rem !important;
                border: 2px solid #e5e7eb !important;
                border-radius: 12px !important;
                font-size: 0.9rem !important;
                transition: all 0.3s ease !important;
                background: rgba(255, 255, 255, 0.95) !important;
                color: #1e293b !important;
            }

            input:focus {
                outline: none !important;
                border-color: #6366f1 !important;
                background: #ffffff !important;
                box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
                transform: translateY(-1px);
            }

            input::placeholder {
                color: #94a3b8 !important;
            }

            label {
                display: block;
                font-weight: 600 !important;
                color: #1e293b !important;
                margin-bottom: 0.5rem !important;
                font-size: 0.875rem !important;
            }

            /* ✅ LOADING SPINNER ANIMATION */
            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            /* Submit Button - NORMAL STATE */
            button[type="submit"],
            .fi-btn,
            .login-btn {
                width: 100% !important;
                padding: 0.8rem !important;
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
                border: none !important;
                border-radius: 12px !important;
                color: white !important;
                font-weight: 600 !important;
                font-size: 0.95rem !important;
                cursor: pointer !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4) !important;
                margin-top: 1rem !important;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            button[type="submit"]:hover,
            .fi-btn:hover,
            .login-btn:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5) !important;
            }

            button[type="submit"]:active,
            .fi-btn:active,
            .login-btn:active {
                transform: translateY(0) !important;
            }

            /* ✅ LOADING STATE - TOMBOL JADI GRAY + SPINNER */
            .login-loading button[type="submit"],
            .login-loading .fi-btn,
            .login-loading .login-btn {
                background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%) !important;
                cursor: wait !important;
                pointer-events: none !important;
                box-shadow: 0 4px 15px rgba(100, 116, 139, 0.3) !important;
            }

            .login-loading button[type="submit"]:hover,
            .login-loading .fi-btn:hover,
            .login-loading .login-btn:hover {
                transform: translateY(0) !important;
            }

            /* ✅ SPINNER ICON */
            .login-spinner {
                display: none;
                width: 18px;
                height: 18px;
                border: 2.5px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top-color: #ffffff;
                animation: spin 0.6s linear infinite;
            }

            .login-loading .login-spinner {
                display: inline-block;
            }

            /* ✅ HIDE TEXT SAAT LOADING */
            .login-loading .login-text {
                opacity: 0.7;
            }

            /* Footer */
            .login-footer {
                text-align: center;
                margin-top: 1.5rem;
                padding-top: 1.25rem;
                border-top: 1px solid rgba(226, 232, 240, 0.8);
            }

            .footer-text {
                font-size: 0.8rem;
                color: #475569;
                margin-bottom: 0.25rem;
                font-weight: 600;
            }

            .footer-copyright {
                font-size: 0.7rem;
                color: #94a3b8;
            }

            /* Hide checkbox */
            input[type="checkbox"],
            .fi-fo-checkbox {
                display: none !important;
            }

            /* Responsive */
            @media (max-width: 640px) {
                .login-card {
                    padding: 2rem 1.5rem;
                    max-width: 100%;
                }

                .logo-wrapper img {
                    width: 85px;
                    height: 85px;
                }

                .app-title {
                    font-size: 1.35rem;
                }
            }
        </style>
    @endpush

    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card" id="loginCard">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-wrapper">
                    <img src="{{ asset('images/logo-rs.png') }}" alt="Logo RS Bhayangkara Jambi">
                </div>
                <h1 class="app-title">
                    APLIKASI INDIKATOR MUTU<br>RS BHAYANGKARA JAMBI
                </h1>
                <p class="app-subtitle">Masuk untuk melanjutkan</p>
            </div>

            <!-- Form -->
            <form wire:submit="authenticate" id="loginForm">
                {{ $this->form }}

                <!-- ✅ TOMBOL LOGIN DENGAN LOADING SPINNER -->
                <button type="submit" class="login-btn">
                    <span class="login-spinner"></span>
                    <span class="login-text">Masuk</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="login-footer">
                <p class="footer-text">Tim Akreditasi RS. Bhayangkara Jambi</p>
                <p class="footer-copyright">© {{ date('Y') }} - All Rights Reserved</p>
            </div>
        </div>
    </div>

    <x-filament-actions::modals />

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const loginCard = document.getElementById('loginCard');
                const loginForm = document.getElementById('loginForm');

                if (loginForm && loginCard) {
                    // ✅ KETIKA FORM DISUBMIT - TAMBAH LOADING STATE
                    loginForm.addEventListener('submit', function(e) {
                        loginCard.classList.add('login-loading');
                    });

                    // ✅ LIVEWIRE LISTENER - RESET LOADING JIKA ADA ERROR
                    document.addEventListener('livewire:init', () => {
                        Livewire.hook('commit', ({
                            component,
                            commit,
                            respond,
                            succeed,
                            fail
                        }) => {
                            // Reset loading state jika validasi gagal
                            fail(() => {
                                loginCard.classList.remove('login-loading');
                            });
                        });
                    });
                }
            });
        </script>
    @endpush
</div>
