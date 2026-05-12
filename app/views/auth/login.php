<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Management System</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-50) 0%, #ffffff 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: var(--space-8);
            border-radius: var(--radius-2xl);
            background: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .login-header h2 {
            font-weight: 700;
            color: var(--neutral-900);
            margin-bottom: var(--space-2);
        }
        
        .login-header p {
            color: var(--neutral-500);
            margin-bottom: var(--space-8);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--neutral-700);
            font-size: var(--text-sm);
        }
        
        .btn-login {
            width: 100%;
            padding: var(--space-3);
            font-weight: 600;
            margin-top: var(--space-4);
        }
    </style>
</head>
<body>

<div class="login-card animate-fade-in">
    <div class="login-header text-center">
        <h2>Welcome Back</h2>
        <p>Sign in to manage your tasks</p>
    </div>
    
    <form id="loginForm" action="<?= url('/api/auth/login') ?>" method="POST">
        <div class="mb-4">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required autofocus>
        </div>
        
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-login">
            Sign In
        </button>
    </form>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="<?= url('assets/js/main.js') ?>"></script>

<script>
    $(document).ready(function() {
        handleFormSubmit('#loginForm', function(response) {
            if (response.redirect) {
                setTimeout(function() {
                    window.location.href = response.redirect;
                }, 1000);
            }
        });
    });
</script>

</body>
</html>
