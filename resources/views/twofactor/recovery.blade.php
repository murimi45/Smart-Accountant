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
                                <i class="fa fa-check-circle me-2"></i>Setup Complete
                            </div>
                            <h2 class="system-title">Recovery Codes</h2>
                            <p class="system-subtitle">Save these codes in a safe place. You'll need them if you lose access to your authenticator app.</p>
                        </div>

                        <div class="benefits-list">
                            <div class="benefit-item">
                                <i class="fa fa-exclamation-triangle benefit-icon"></i>
                                <span>Each code can only be used once</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-exclamation-triangle benefit-icon"></i>
                                <span>Store them securely (password manager recommended)</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-exclamation-triangle benefit-icon"></i>
                                <span>Never share these codes with anyone</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-exclamation-triangle benefit-icon"></i>
                                <span>These codes will not be shown again</span>
                            </div>
                        </div>

                        <div class="security-badge mt-5">
                            <i class="fa fa-shield-alt me-2"></i>
                            <span>Your account is now protected with 2FA</span>
                        </div>
                    </div>
                </div>

                {{-- Right Side - Recovery Codes --}}
                <div class="col-lg-6 form-side">
                    <div class="login-wrapper">
                        <div class="login_section">
                            {{-- Header --}}
                            <div class="form-header mb-4">
                                <div class="icon-badge success mb-3">
                                    <i class="fa fa-check"></i>
                                </div>
                                <h3 class="form-title">2FA Enabled Successfully!</h3>
                                <p class="form-subtitle">Save your recovery codes before continuing</p>
                            </div>

                            {{-- Important Notice --}}
                            <div class="alert alert-warning" role="alert">
                                <div class="d-flex align-items-start">
                                    <i class="fa fa-exclamation-triangle me-2 mt-1"></i>
                                    <div>
                                        <strong>Important!</strong> These codes will only be shown once. Save them now.
                                    </div>
                                </div>
                            </div>

                            {{-- Recovery Codes Grid --}}
                            <div class="recovery-codes-section mb-4">
                                <label class="label_field mb-3">Your Recovery Codes:</label>
                                <div class="recovery-codes-grid" id="recovery-codes">
                                    @foreach($codes as $index => $code)
                                        <div class="recovery-code-item">
                                            <span class="code-number">{{ $index + 1 }}</span>
                                            <span class="code-value">{{ $code }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="action-buttons mb-4">
                                <button type="button" class="btn btn-outline-secondary w-100 mb-2" onclick="downloadCodes()">
                                    <i class="fa fa-download me-2"></i>Download as Text File
                                </button>
                                <button type="button" class="btn btn-outline-secondary w-100 mb-2" onclick="printCodes()">
                                    <i class="fa fa-print me-2"></i>Print Recovery Codes
                                </button>
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="copyCodes()">
                                    <i class="fa fa-copy me-2"></i>Copy to Clipboard
                                </button>
                            </div>

                            {{-- Continue Button --}}
                            <div class="field mb-3">
                                <a href="{{ route('dashboard') }}" class="btn btn-success w-100 submit-btn">
                                    <i class="fa fa-arrow-right me-2"></i>Continue to Dashboard
                                </a>
                            </div>

                            {{-- Footer --}}
                            <div class="text-center mt-4">
                                <p class="footer-text">
                                    <i class="fa fa-lock me-1"></i>
                                    You can view these codes again in your account settings
                                </p>
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
            max-width: 500px;
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

        .icon-badge.success {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
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

        /* Recovery Codes Section */
        .recovery-codes-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #e9ecef;
        }

        .label_field {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            display: block;
        }

        .recovery-codes-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .recovery-code-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
        }

        .recovery-code-item:hover {
            border-color: #79c347;
            box-shadow: 0 2px 8px rgba(121, 195, 71, 0.15);
        }

        .code-number {
            background: #79c347;
            color: white;
            font-size: 11px;
            font-weight: 600;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .code-value {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            letter-spacing: 1px;
        }

        /* Action Buttons */
        .action-buttons .btn {
            font-size: 14px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary {
            border: 1px solid #dee2e6;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #f8f9fa;
            border-color: #79c347;
            color: #79c347;
            transform: translateY(-1px);
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

            .recovery-codes-grid {
                grid-template-columns: 1fr;
            }

            .code-value {
                font-size: 13px;
            }
        }
    </style>

    <script>
        const codes = @json($codes);

        // Download codes as text file
        function downloadCodes() {
            const content = 'Two-Factor Authentication Recovery Codes\n' +
                           'Generated: ' + new Date().toLocaleString() + '\n' +
                           'Account: {{ auth()->user()->email }}\n\n' +
                           'IMPORTANT: Keep these codes safe and secure.\n' +
                           'Each code can only be used once.\n\n' +
                           'Recovery Codes:\n' +
                           codes.map((code, i) => `${i + 1}. ${code}`).join('\n');
            
            const blob = new Blob([content], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = '2fa-recovery-codes.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        // Print codes
        function printCodes() {
            const printContent = `
                <html>
                <head>
                    <title>2FA Recovery Codes</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 40px; }
                        h1 { color: #2c3e50; }
                        .warning { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
                        .codes { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 20px 0; }
                        .code { padding: 10px; border: 1px solid #dee2e6; border-radius: 4px; font-family: monospace; }
                    </style>
                </head>
                <body>
                    <h1>Two-Factor Authentication Recovery Codes</h1>
                    <p><strong>Account:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Generated:</strong> ${new Date().toLocaleString()}</p>
                    <div class="warning">
                        <strong>⚠️ Important:</strong> Keep these codes safe and secure. Each code can only be used once.
                    </div>
                    <div class="codes">
                        ${codes.map((code, i) => `<div class="code">${i + 1}. ${code}</div>`).join('')}
                    </div>
                </body>
                </html>
            `;
            
            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }

        // Copy codes to clipboard
        function copyCodes() {
            const btn = event.target.closest('button');
            const text = codes.map((code, i) => `${i + 1}. ${code}`).join('\n');
            navigator.clipboard.writeText(text).then(function() {
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fa fa-check me-2"></i>Copied!';
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-success');
                setTimeout(function() {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                }, 2000);
            });
        }
    </script>

    @include('layouts.footer')
</body>