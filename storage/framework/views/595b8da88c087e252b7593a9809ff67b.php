<?php echo $__env->make('layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<body class="inner_page login">
    <div class="full_container">
        <div class="container-fluid">
            <div class="row min-vh-100 align-items-center">
                
                <div class="col-lg-6 description-side d-none d-lg-block">
                    <div class="description-content">
                        <div class="logo-section mb-5">
                            <div class="welcome-badge mb-3">
                                <i class="fa fa-check-circle me-2"></i>Trusted by 500+ Schools
                            </div>
                            <h2 class="system-title">Welcome Back!</h2>
                            <p class="system-subtitle">Access your school's financial dashboard and manage everything in one place.</p>
                        </div>

                        <div class="stats-grid mb-5">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>10,000+</h3>
                                    <p>Active Users</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-chart-line"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>99.9%</h3>
                                    <p>Uptime</p>
                                </div>
                            </div>
                        </div>

                        <div class="benefits-list">
                            <div class="benefit-item">
                                <i class="fa fa-check-circle benefit-icon"></i>
                                <span>Real-time financial insights and reports</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-check-circle benefit-icon"></i>
                                <span>Automated fee collection and reminders</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-check-circle benefit-icon"></i>
                                <span>Secure cloud-based data storage</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-check-circle benefit-icon"></i>
                                <span>24/7 customer support</span>
                            </div>
                        </div>

                        <div class="security-badge mt-5">
                            <i class="fa fa-shield-alt me-2"></i>
                            <span>Bank-grade 256-bit SSL encryption</span>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-6 form-side">
                    <div class="login-wrapper">
                        <div class="login_section">
                            
                            <div class="form-header mb-4">
                                <h3 class="form-title">Sign In</h3>
                                <p class="form-subtitle">Enter your credentials to access your account</p>
                            </div>

                            
                            <div class="login_form">
                                <?php if($errors->any()): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="fa fa-exclamation-circle me-2 mt-1"></i>
                                            <div class="flex-grow-1">
                                                <strong>Login Failed!</strong>
                                                <ul class="mb-0 mt-1 ps-3">
                                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li><?php echo e($error); ?></li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form action="<?php echo e(route('login')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <fieldset>
                                        
                                        <div class="field mb-3">
                                            <label for="email" class="label_field">Email Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fa fa-envelope"></i>
                                                </span>
                                                <input type="email" 
                                                       id="email" 
                                                       name="email" 
                                                       class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       placeholder="Enter your email address"
                                                       value="<?php echo e(old('email')); ?>"
                                                       required 
                                                       autofocus />
                                            </div>
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <small class="text-danger"><?php echo e($message); ?></small>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        
                                        <div class="field mb-3">
                                            <label for="password" class="label_field">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fa fa-lock"></i>
                                                </span>
                                                <input type="password" 
                                                       id="password" 
                                                       name="password" 
                                                       class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       placeholder="Enter your password"
                                                       required />
                                                <button type="button" 
                                                        class="btn btn-outline-secondary" 
                                                        id="togglePassword">
                                                    <i class="fa fa-eye" id="eyeIcon"></i>
                                                </button>
                                            </div>
                                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <small class="text-danger"><?php echo e($message); ?></small>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        
                                        <div class="field mb-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           class="form-check-input" 
                                                           id="remember" 
                                                           name="remember">
                                                    <label class="form-check-label" for="remember">
                                                        Remember Me
                                                    </label>
                                                </div>
                                                <a href="#" class="forgot-link">
                                                    Forgot Password?
                                                </a>
                                            </div>
                                        </div>

                                        
                                        <div class="field mb-3">
                                            <button type="submit" class="btn btn-success w-100 submit-btn">
                                                <i class="fa fa-sign-in-alt me-2"></i>Sign In
                                            </button>
                                        </div>

                                        
                                        <div class="text-center mt-4 signup-section">
                                            <p class="signup-text">
                                                Don't have an account?
                                                <a href="<?php echo e(route('register')); ?>" class="signup-link">Create Account</a>
                                            </p>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>

                            
                            <div class="text-center mt-4">
                                <p class="footer-text">
                                    <i class="fa fa-lock me-1"></i>
                                    Secure login • All data encrypted
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
        }

        .stat-info h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: white;
        }

        .stat-info p {
            font-size: 13px;
            margin: 0;
            color: rgba(255, 255, 255, 0.85);
        }

        /* Benefits List */
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

        /* Form Inputs */
        .label_field {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }

        .input-group {
            margin-bottom: 5px;
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

        .form-control:focus {
            border-color: #79c347;
            box-shadow: 0 0 0 0.2rem rgba(121, 195, 71, 0.15);
            outline: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #79c347;
            background: #f0f8e8;
        }

        /* Checkbox & Links */
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
            color: #6c757d;
            cursor: pointer;
            margin: 0;
        }

        .forgot-link {
            font-size: 14px;
            color: #79c347;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-link:hover {
            color: #5fa732;
            text-decoration: underline;
        }

        /* Buttons */
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

        #togglePassword {
            border: 1px solid #dee2e6;
            border-left: none;
            background: white;
            padding: 12px 15px;
        }

        #togglePassword:hover {
            background: #f8f9fa;
        }

        /* Sign Up Section */
        .signup-section {
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .signup-text {
            font-size: 14px;
            color: #6c757d;
            margin: 0;
        }

        .signup-link {
            color: #79c347;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .signup-link:hover {
            color: #5fa732;
            text-decoration: underline;
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

        /* Responsive Design */
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

            .submit-btn {
                padding: 12px;
                font-size: 15px;
            }
        }
    </style>

    <script>
        // Toggle Password Visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>

    <?php echo $__env->make('layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/auth/login.blade.php ENDPATH**/ ?>