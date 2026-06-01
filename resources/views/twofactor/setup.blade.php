@include('layouts.header')
<body class="inner_page login">
    <div class="full_container">
        <div class="container-fluid">
            <div class="row align-items-center">
                {{-- Left Side - Description --}}
                <div class="col-lg-6 description-side d-none d-lg-block">
                    <div class="description-content">
                        <div class="logo-section mb-5">
                            <div class="welcome-badge mb-3">
                                <i class="fa fa-shield-alt me-2"></i>Enhanced Security
                            </div>
                            <h2 class="system-title">Two-Factor Authentication</h2>
                            <p class="system-subtitle">Add an extra layer of security to your account by enabling two-factor authentication.</p>
                        </div>

                        <div class="benefits-list">
                            <div class="benefit-item">
                                <i class="fa fa-check-circle benefit-icon"></i>
                                <span>Protect your account from unauthorized access</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-check-circle benefit-icon"></i>
                                <span>Industry-standard TOTP authentication</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-check-circle benefit-icon"></i>
                                <span>Works with Google Authenticator, Authy, and more</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-check-circle benefit-icon"></i>
                                <span>Recovery codes for backup access</span>
                            </div>
                        </div>

                        <div class="security-badge mt-5">
                            <i class="fa fa-lock me-2"></i>
                            <span>Required for {{ auth()->user()->role }} accounts</span>
                        </div>
                    </div>
                </div>

                {{-- Right Side - Setup Form --}}
                <div class="col-lg-6 form-side">
                    <div class="login-wrapper">
                        <div class="login_section">
                            {{-- Header --}}
                            <div class="form-header mb-4">
                                <div class="icon-badge mb-3">
                                    <i class="fa fa-mobile-alt"></i>
                                </div>
                                <h3 class="form-title">Enable 2FA</h3>
                                <p class="form-subtitle">Scan the QR code with your authenticator app</p>
                            </div>

                            {{-- Alerts --}}
                            @if(session('info'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="fa fa-info-circle me-2"></i>{{ session('info') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(session('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <i class="fa fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Instructions --}}
                            <div class="setup-instructions mb-4">
                                <h6 class="instructions-title">Setup Steps:</h6>
                                <ol class="instructions-list">
                                    <li>Install an authenticator app (Google Authenticator, Authy, etc.)</li>
                                    <li>Scan the QR code below</li>
                                    <li>Enter the 6-digit code to verify</li>
                                </ol>
                            </div>

                            {{-- QR Code --}}
                            <div class="qr-code-section mb-4">
                                <div class="qr-code-wrapper">
                                    {!! $qrCodeSvg !!}
                                </div>
                            </div>

                            {{-- Manual Entry Code --}}
                            <div class="manual-code-section mb-4">
                                <label class="manual-code-label">Can't scan? Enter manually:</label>
                                <div class="manual-code-box">
                                    <code>{{ $secret }}</code>
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copySecret()">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Verification Form --}}
                            <div class="login_form">
                                <form method="POST" action="{{ route('twofactor.confirm') }}">
                                    @csrf
                                    <fieldset>
                                        <div class="field mb-3">
                                            <label for="code" class="label_field">Verification Code</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fa fa-key"></i>
                                                </span>
                                                <input type="text" 
                                                       id="code" 
                                                       name="code" 
                                                       maxlength="6"
                                                       pattern="[0-9]{6}"
                                                       class="form-control code-input @error('code') is-invalid @enderror" 
                                                       placeholder="000000"
                                                       required 
                                                       autofocus />
                                            </div>
                                            @error('code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="field mb-3">
                                            <button type="submit" class="btn btn-success w-100 submit-btn">
                                                <i class="fa fa-check me-2"></i>Verify and Enable 2FA
                                            </button>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>

                            {{-- Footer --}}
                            <div class="text-center mt-4">
                                <p class="footer-text mb-2">
                                    Logged in as <strong>{{ auth()->user()->email }}</strong>
                                </p>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm text-muted">
                                        <i class="fa fa-sign-out-alt me-1"></i>Log out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
  /* Base Styles */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body.login {
    background: #ffffff;
    margin: 0;
    padding: 0;
}

.full_container {
    padding: 0;
    margin: 0;
}

.container-fluid {
    padding: 0;
    margin: 0;
}

.row {
    margin: 0;
}

.col-lg-6 {
    padding: 0;
}

/* Left Side - Description */
.description-side {
    background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
    padding: 60px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    position: fixed;
    left: 0;
    top: 0;
    width: 50%;
    height: 100vh;
    overflow: hidden;
}

.description-side::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

.description-side::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -30%;
    width: 80%;
    height: 80%;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

.description-content {
    max-width: 500px;
    position: relative;
    z-index: 1;
}

.welcome-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: white;
    backdrop-filter: blur(10px);
}

.system-title {
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 15px;
    color: white;
    line-height: 1.2;
}

.system-subtitle {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0;
    line-height: 1.6;
}

.benefits-list {
    margin-top: 40px;
}

.benefit-item {
    display: flex;
    align-items: center;
    margin-bottom: 18px;
    font-size: 15px;
    color: white;
}

.benefit-icon {
    font-size: 18px;
    margin-right: 12px;
    color: rgba(255, 255, 255, 0.9);
}

.security-badge {
    background: rgba(255, 255, 255, 0.15);
    padding: 15px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    color: white;
    backdrop-filter: blur(10px);
    display: inline-flex;
    align-items: center;
}

/* Right Side - Form */
.form-side {
    background: #f8f9fa;
    padding: 60px 40px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    min-height: 100vh;
    margin-left: 50%;
    width: 50%;
}

.login-wrapper {
    width: 100%;
    max-width: 450px;
    padding: 20px 0;
}

.login_section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 40px;
}

.form-header {
    text-align: center;
}

.icon-badge {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.icon-badge i {
    font-size: 28px;
    color: white;
}

.form-title {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.form-subtitle {
    font-size: 14px;
    color: #6c757d;
    margin: 0;
}

/* Setup Instructions */
.setup-instructions {
    background: #f8f9fa;
    border-left: 4px solid #79c347;
    padding: 15px 20px;
    border-radius: 8px;
}

.instructions-title {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.instructions-list {
    margin: 0;
    padding-left: 20px;
    font-size: 13px;
    color: #6c757d;
}

.instructions-list li {
    margin-bottom: 6px;
}

/* QR Code Section */
.qr-code-section {
    text-align: center;
}

.qr-code-wrapper {
    display: inline-block;
    background: white;
    padding: 20px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.qr-code-wrapper svg {
    display: block;
}

/* Manual Code Section */
.manual-code-section {
    text-align: center;
}

.manual-code-label {
    font-size: 13px;
    color: #6c757d;
    display: block;
    margin-bottom: 10px;
}

.manual-code-box {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 12px 16px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.manual-code-box code {
    font-family: 'Courier New', monospace;
    font-size: 14px;
    color: #2c3e50;
    font-weight: 600;
    letter-spacing: 1px;
}

.copy-btn {
    padding: 4px 8px;
    font-size: 12px;
}

/* Form Inputs */
.label_field {
    font-size: 14px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    display: block;
}

.input-group-text {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-right: none;
    padding: 12px 15px;
}

.input-group-text i {
    color: #6c757d;
    font-size: 14px;
}

.form-control {
    border: 1px solid #dee2e6;
    border-left: none;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.code-input {
    text-align: center;
    font-size: 24px;
    font-family: 'Courier New', monospace;
    letter-spacing: 8px;
    font-weight: 600;
}

.form-control:focus {
    border-color: #79c347;
    box-shadow: 0 0 0 0.2rem rgba(121, 195, 71, 0.15);
    outline: none;
}

.input-group:focus-within .input-group-text {
    border-color: #79c347;
    background: #f0f8e8;
}

/* Submit Button */
.submit-btn {
    background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
    border: none;
    padding: 14px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(121, 195, 71, 0.4);
}

/* Footer */
.footer-text {
    font-size: 13px;
    color: #9ca3af;
    margin: 0;
}

/* Alert */
.alert {
    border-radius: 8px;
    font-size: 14px;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 991px) {
    html, body {
        overflow: auto;
    }

    .description-side {
        display: none !important;
    }

    .form-side {
        padding: 40px 20px;
        min-height: 100vh;
        margin-left: 0;
        width: 100%;
    }

    .login_section {
        padding: 30px 25px;
    }

    .full_container {
        height: auto;
        overflow: visible;
    }

    .row {
        height: auto;
    }
}

@media (max-width: 576px) {
    .form-title {
        font-size: 24px;
    }

    .login_section {
        padding: 25px 20px;
    }

    .code-input {
        font-size: 20px;
        letter-spacing: 4px;
    }
}
    </style>

    <script>
        // Auto-submit when 6 digits entered
        document.getElementById('code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 6) {
                this.form.submit();
            }
        });

        // Copy secret to clipboard
        function copySecret() {
            const secret = '{{ $secret }}';
            navigator.clipboard.writeText(secret).then(function() {
                const btn = document.querySelector('.copy-btn');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fa fa-check"></i>';
                setTimeout(function() {
                    btn.innerHTML = originalHTML;
                }, 2000);
            });
        }
    </script>

    @include('layouts.footer')
</body>