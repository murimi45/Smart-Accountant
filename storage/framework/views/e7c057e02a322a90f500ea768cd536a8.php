<?php echo $__env->make('layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<body class="inner_page login">
    <div class="full_container">
        <div class="container-fluid">
            <div class="row min-vh-100 ">
                
                <div class="col-lg-6 description-side d-none d-lg-block">
                    <div class="description-content">
                        <div class="logo-section mb-5">
                            <h2 class="system-title">School Accounting System</h2>
                            <p class="system-subtitle">Streamline Your School's Financial Management</p>
                        </div>

                        <div class="features-list">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fa fa-chart-line"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Financial Tracking</h4>
                                    <p>Monitor all income and expenses in real-time with comprehensive reporting and analytics.</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fa fa-receipt"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Fee Management</h4>
                                    <p>Automate fee collection, generate receipts, and track payments effortlessly.</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Multi-User Access</h4>
                                    <p>Secure role-based access for administrators, teachers, and accountants.</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fa fa-file-invoice"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Smart Reporting</h4>
                                    <p>Generate detailed financial reports and statements with just a few clicks.</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fa fa-shield-alt"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Secure & Reliable</h4>
                                    <p>Bank-level security to protect your sensitive financial data.</p>
                                </div>
                            </div>
                        </div>

                        <div class="testimonial mt-5">
                            <div class="testimonial-content">
                                <i class="fa fa-quote-left quote-icon"></i>
                                <p class="testimonial-text">"This system has transformed how we manage our school finances. Everything is organized and transparent."</p>
                                <p class="testimonial-author">- School Administrator</p>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-6 form-side">
                    <div class="registration-wrapper">
                        <div class="login_section register_section">
                            
                            <div class="form-header mb-4">
                                <h3 class="form-title">Create Your Account</h3>
                                <p class="form-subtitle">Get started with your school's financial management</p>
                            </div>

                            
                            <div class="login_form">
                                <?php if($errors->any()): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="fa fa-exclamation-circle me-2 mt-1"></i>
                                            <div class="flex-grow-1">
                                                <strong>Registration Failed!</strong>
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

                                <form action="<?php echo e(route('register')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <fieldset>
                                        
                                        <div class="form-section-header mb-3">
                                            <h6 class="section-title">
                                                <i class="fa fa-school me-2"></i>
                                                School Information
                                            </h6>
                                        </div>

                                        
                                        <div class="field mb-3">
                                            <label for="school_name" class="label_field">School Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fa fa-school"></i>
                                                </span>
                                                <input type="text" 
                                                       id="school_name" 
                                                       name="school_name" 
                                                       class="form-control <?php $__errorArgs = ['school_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       placeholder="Enter school name"
                                                       value="<?php echo e(old('school_name')); ?>"
                                                       required />
                                            </div>
                                            <?php $__errorArgs = ['school_name'];
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
                                                       placeholder="school@example.com"
                                                       value="<?php echo e(old('email')); ?>"
                                                       required />
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
                                            <label for="phone" class="label_field">Phone Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fa fa-phone"></i>
                                                </span>
                                                <input type="text" 
                                                       id="phone" 
                                                       name="phone" 
                                                       class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       placeholder="+254 XXX XXX XXX"
                                                       value="<?php echo e(old('phone')); ?>"
                                                       required />
                                            </div>
                                            <?php $__errorArgs = ['phone'];
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
                                            <label for="address" class="label_field">Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fa fa-map-marker-alt"></i>
                                                </span>
                                                <input type="text" 
                                                       id="address" 
                                                       name="address" 
                                                       class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       placeholder="Enter school address"
                                                       value="<?php echo e(old('address')); ?>"
                                                       required />
                                            </div>
                                            <?php $__errorArgs = ['address'];
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

                                        
                                        <div class="form-section-header mb-3 mt-4">
                                            <h6 class="section-title">
                                                <i class="fa fa-user-shield me-2"></i>
                                                Administrator Details
                                            </h6>
                                        </div>

                                        
                                        <div class="field mb-3">
                                            <label for="admin_name" class="label_field">Admin Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fa fa-user"></i>
                                                </span>
                                                <input type="text" 
                                                       id="admin_name" 
                                                       name="admin_name" 
                                                       class="form-control <?php $__errorArgs = ['admin_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       placeholder="Enter administrator name"
                                                       value="<?php echo e(old('admin_name')); ?>"
                                                       required />
                                            </div>
                                            <?php $__errorArgs = ['admin_name'];
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
                                                       placeholder="Create a strong password"
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
                                            <small class="text-muted form-hint">
                                                <i class="fa fa-info-circle me-1"></i>Must be at least 8 characters
                                            </small>
                                        </div>

                                        
                                         <div class="field mb-4">
                                             <label for="password_confirmation" class="label_field">Confirm Password</label>
                                             <div class="input-group">
                                                 <span class="input-group-text">
                                                     <i class="fa fa-lock"></i>
                                                 </span>
                                                 <input type="password" 
                                                        id="password_confirmation" 
                                                        name="password_confirmation" 
                                                        class="form-control <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                        placeholder="Re-enter your password"
                                                        required />
                                                 <button type="button" 
                                                         class="btn btn-outline-secondary" 
                                                         id="toggleConfirmPassword">
                                                     <i class="fa fa-eye" id="eyeIconConfirm"></i>
                                                 </button>
                                             </div>
                                             <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                 <small class="text-danger"><?php echo e($message); ?></small>
                                             <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                             <small class="text-muted form-hint">
                                                 <i class="fa fa-info-circle me-1"></i>Re-enter the same password for confirmation
                                             </small>
                                         </div>


                                        
                                        <div class="field mb-3">
                                            <button type="submit" class="btn btn-primary w-100 submit-btn">
                                                <i class="fa fa-check-circle me-2"></i>Create Account
                                            </button>
                                        </div>

                                        
                                        <div class="text-center mt-4 signin-section">
                                            <p class="signin-text">
                                                Already have an account?
                                                <a href="<?php echo e(route('login')); ?>" class="signin-link">Sign In</a>
                                            </p>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>

                            
                            <div class="text-center mt-4">
                                <p class="footer-text">
                                    <i class="fa fa-shield-alt me-1"></i>
                                    By registering, you agree to our Terms of Service
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;  /* ADD THIS */
            position: relative;
        }

        .description-content {
            max-width: 500px;
        }

        .system-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
            color: white;
        }

        .system-subtitle {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0;
        }

        .features-list {
            margin-top: 50px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 35px;
        }

        .feature-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .feature-icon i {
            font-size: 22px;
            color: white;
        }

        .feature-content h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: white;
        }

        .feature-content p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.85);
            margin: 0;
            line-height: 1.6;
        }

        .testimonial {
            background: rgba(255, 255, 255, 0.15);
            padding: 25px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .quote-icon {
            font-size: 24px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 15px;
        }

        .testimonial-text {
            font-size: 15px;
            font-style: italic;
            color: white;
            margin-bottom: 12px;
            line-height: 1.6;
        }

        .testimonial-author {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
            font-weight: 500;
        }

        /* Right Side - Form */
        .form-side {
            background: #f8f9fa;
            padding: 60px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .registration-wrapper {
            width: 100%;
            max-width: 500px;
        }

        .register_section {
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

        .form-section-header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .section-title {
            font-size: 15px;
            font-weight: 600;
            color: #495057;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .section-title i {
            color: #667eea;
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
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
            outline: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #667eea;
            background: #f0f2ff;
        }

        .form-hint {
            font-size: 12px;
            display: block;
            margin-top: 5px;
            color: #6c757d;
        }

        /* Buttons */
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
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

        /* Sign In Section */
        .signin-section {
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .signin-text {
            font-size: 14px;
            color: #6c757d;
            margin: 0;
        }

        .signin-link {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .signin-link:hover {
            color: #764ba2;
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

            .register_section {
                padding: 30px 25px;
            }
        }

        @media (max-width: 576px) {
            .form-title {
                font-size: 24px;
            }

            .register_section {
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


        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const eyeIconConfirm = document.getElementById('eyeIconConfirm');
            
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                eyeIconConfirm.classList.remove('fa-eye');
                eyeIconConfirm.classList.add('fa-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                eyeIconConfirm.classList.remove('fa-eye-slash');
                eyeIconConfirm.classList.add('fa-eye');
            }
        });
    </script>

    <?php echo $__env->make('layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/auth/register.blade.php ENDPATH**/ ?>