<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | Alumni Connect</title>
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
            <i class="fas fa-exclamation-triangle text-8xl text-gray-300 mb-4"></i>
            <h1 class="text-9xl font-bold text-primary">500</h1>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Server Error</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Something went wrong on our end. We're working to fix it. Please try again later.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/AlumniConnect/" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                <i class="fas fa-home mr-2"></i>Go Home
            </a>
            <button onclick="location.reload()" class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-redo mr-2"></i>Try Again
            </button>
        </div>
    </div>
</body>
</html>
