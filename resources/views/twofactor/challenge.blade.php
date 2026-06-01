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
                                <i class="fa fa-lock me-2"></i>Secure Access
                            </div>
                            <h2 class="system-title">Two-Factor Authentication</h2>
                            <p class="system-subtitle">Enter the 6-digit code from your authenticator app to access your account.</p>
                        </div>

                        <div class="benefits-list">
                            <div class="benefit-item">
                                <i class="fa fa-mobile-alt benefit-icon"></i>
                                <span>Open your authenticator app (Google Authenticator, Authy, etc.)</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-hashtag benefit-icon"></i>
                                <span>Find the 6-digit code for {{ config('app.name') }}</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-keyboard benefit-icon"></i>
                                <span>Enter the code to verify your identity</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-clock benefit-icon"></i>
                                <span>Codes refresh every 30 seconds</span>
                            </div>
                        </div>

                        <div class="security-badge mt-5">
                            <i class="fa fa-shield-alt me-2"></i>
                            <span>Your account is protected with 2FA</span>
                        </div>
                    </div>
                </div>

                {{-- Right Side - Challenge Form --}}
                <div class="col-lg-6 form-side">
                    <div class="login-wrapper">
                        <div class="login_section">
                            {{-- Header --}}
                            <div class="form-header mb-4">
                                <div class="icon-badge mb-3">
                                    <i class="fa fa-shield-alt"></i>
                                </div>
                                <h3 class="form-title">Verify Your Identity</h3>
                                <p class="form-subtitle">Enter your authentication code</p>
                            </div>

                            {{-- User Info --}}
                            <div class="user-info-box mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name">{{ auth()->user()->admin_name }}</div>
                                        <div class="user-email">{{ auth()->user()->email }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Error Messages --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fa fa-exclamation-circle me-2"></i>
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Challenge Form --}}
                            <div class="login_form">
                                <form method="POST" action="{{ route('twofactor.challenge.verify') }}">
                                    @csrf
                                    <fieldset>
                                        <div class="field mb-3">
                                            <label for="code" class="label_field">Authentication Code</label>
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
                                                       autofocus 
                                                       autocomplete="off" />
                                            </div>
                                            @error('code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        {{-- Trust Device Option --}}
                                        <div class="field mb-4">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       id="trust_device" 
                                                       name="trust_device">
                                                <label class="form-check-label" for="trust_device">
                                                    Trust this device for 30 days
                                                </label>
                                            </div>
                                            <small class="text-muted">You won't be asked for a code on this device for 30 days</small>
                                        </div>

                                        {{-- Submit Button --}}
                                        <div class="field mb-3">
                                            <button type="submit" class="btn btn-success w-100 submit-btn">
                                                <i class="fa fa-check me-2"></i>Verify Code
                                            </button>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>

                            {{-- Recovery Code Link --}}
                            <div class="text-center mt-4 recovery-link-section">
                                <button type="button" class="btn btn-link text-muted" data-bs-toggle="collapse" data-bs-target="#recoveryCodeSection">
                                    <i class="fa fa-question-circle me-1"></i>Lost your device? Use a recovery code
                                </button>
                                
                                <div class="collapse mt-3" id="recoveryCodeSection">
                                    <div class="recovery-code-box">
                                        <p class="small text-muted mb-2">Enter one of your recovery codes instead:</p>
                                        <form method="POST" action="{{ route('twofactor.challenge.verify') }}">
                                            @csrf
                                            <div class="input-group mb-2">
                                                <input type="text" 
                                                       name="code" 
                                                       class="form-control" 
                                                       placeholder="Enter recovery code"
                                                       required />
                                                <button type="submit" class="btn btn-secondary">
                                                    Use Code
                                                </button>
                                            </div>
                                            <small class="text-muted">Each recovery code can only be used once</small>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Logout Option --}}
                            <div class="text-center mt-4">
                                <form method="POST" action="{{ route('logout') }}">
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
        body.login {
            background: #ffffff;
            min-height: 100vh;
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
            min-height: 100vh;
        }

        /* Left Side - Description */
        .description-side {
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            padding: 60px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
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
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-wrapper {
            width: 100%;
            max-width: 450px;
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

        /* User Info Box */
        .user-info-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .user-avatar i {
            font-size: 24px;
            color: white;
        }

        .user-details {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .user-email {
            color: #6c757d;
            font-size: 13px;
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

        /* Checkbox */
        .form-check {
            display: flex;
            align-items: center;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            margin-right: 8px;
        }

        .form-check-input:checked {
            background-color: #79c347;
            border-color: #79c347;
        }

        .form-check-label {
            font-size: 14px;
            color: #495057;
            cursor: pointer;
            margin: 0;
            font-weight: 500;
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

        /* Recovery Code Section */
        .recovery-link-section {
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .recovery-code-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
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
            .form-side {
                padding: 40px 20px;
            }

            .login_section {
                padding: 30px 25px;
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

        // Clear input on error
        @if ($errors->any())
            document.getElementById('code').value = '';
            document.getElementById('code').focus();
        @endif
    </script>

    @include('layouts.footer')
</body>