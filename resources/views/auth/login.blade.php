<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodPoint - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#E32636',
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen flex">
    <!-- Panel Izquierdo - Imagen del Restaurante -->
    <div class="hidden lg:flex lg:w-1/2 relative">
        <div class="absolute inset-0 bg-black bg-opacity-40 z-10"></div>
        <img src="/images/restaurant-interior.jpg" 
             alt="Interior del restaurante" 
             class="w-full h-full object-cover">
        <div class="absolute inset-0 z-20 flex flex-col justify-center items-center text-white p-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">FoodPoint</h1>
                <p class="text-xl mb-6">Sistema de Gestión de Restaurante</p>
                <div class="max-w-md">
                    <p class="text-lg opacity-90 leading-relaxed">
                        Gestiona tu restaurante con elegancia y eficiencia. 
                        Control total de pedidos, inventario, mesas y ventas.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Derecho - Formulario de Login -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 p-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo para móviles -->
            <div class="lg:hidden text-center mb-8">
                <div class="mx-auto h-16 w-16 bg-primary rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-utensils text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">FoodPoint</h1>
                <p class="text-gray-600">Sistema de Gestión de Restaurante</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-xl">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Bienvenido</h2>
                    <p class="mt-2 text-gray-600">Inicia sesión para continuar</p>
                </div>
            
            <form class="mt-8 space-y-6" action="/login" method="POST">
                @csrf
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                               placeholder="Correo electrónico" value="{{ old('email') }}">
                    </div>
                    <div>
                        <label for="password" class="sr-only">Contraseña</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                               placeholder="Contraseña">
                    </div>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Error en el inicio de sesión
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" 
                               class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                            Recordarme
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-primary hover:text-red-500">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-primary hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-lock text-red-300 group-hover:text-red-200"></i>
                        </span>
                        Iniciar Sesión
                    </button>
                </div>

                <div class="text-center">
                    <div class="text-sm text-gray-600">
                        <p class="mb-2">Credenciales de prueba:</p>
                        <p class="font-mono text-xs bg-gray-100 p-2 rounded">
                            <strong>Email:</strong> admin@restaurant.com<br>
                            <strong>Contraseña:</strong> password
                        </p>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</body>
</html>