<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied | Alumni Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7A1E1E',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <i class="fas fa-lock text-8xl text-gray-300 mb-4"></i>
            <h1 class="text-9xl font-bold text-primary">403</h1>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Access Denied</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            You don't have permission to access this resource. Please login or contact support if you believe this is an error.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/AlumniConnect/login/login.php" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
            </a>
            <a href="/AlumniConnect/" class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-home mr-2"></i>Go Home
            </a>
        </div>
    </div>
</body>
</html>
