<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Modacol</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
        }
        
        a {
            text-decoration: none !important;
        }
        
        .bt_login {
            background: #5E5DF0;
            border-radius: 999px;
            box-shadow: #fafafc 0 10px 20px -10px;
            box-sizing: border-box;
            color: #FFFFFF;
            cursor: pointer;
            font-size: 20px;
            font-weight: 700;
            line-height: 24px;
            opacity: 1;
            outline: 0 solid transparent;
            padding: 12px 30px;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            width: fit-content;
            word-break: break-word;
            border: 0;
            transition: all 0.3s ease;
        }
        
        .bt_login:hover {
            background: #4A4AF0;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px -10px rgba(94, 93, 240, 0.4);
        }
        
        .custom-input {
            border-radius: 8px;
            border: 1px solid #E1E1E1;
            transition: all 0.3s ease;
        }
        
        .custom-input:focus {
            border-color: #5E5DF0;
            box-shadow: 0 0 0 3px rgba(94, 93, 240, 0.2);
        }
        
        .remember-checkbox:checked {
            background-color: #5E5DF0;
            border-color: #5E5DF0;
        }
        
        @media (min-width: 768px) {
            .login-container {
                height: 100vh !important;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50">
    <div class="flex flex-col lg:flex-row min-h-screen login-container">
        <!-- Sección del formulario -->
        <div class="w-full lg:w-2/5 flex items-center justify-center py-8 px-6"> 
            <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <img src="/Images/logo.png" class="w-48 max-w-xs mx-auto" alt="Logo de Modacol"/>
                </div>

                <h2 class="text-3xl font-bold mb-2 text-center text-gray-800">Bienvenido de nuevo</h2>
                <p class="text-center mb-8 text-gray-600">Ingresa tus credenciales para continuar</p>
                
                <!-- Mensajes de error -->
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="Correo">
                            Correo electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input type="email" name="Correo" id="Correo" 
                                   class="custom-input pl-10 shadow appearance-none rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                   value="{{ old('Correo') }}" required autofocus placeholder="email@example.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                            Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" 
                                   class="custom-input pl-10 shadow appearance-none rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                   required placeholder="Contraseña">
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="remember-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-gray-700 text-sm">Recuérdame</span>
                        </label>
                        
                        @if(Route::has('password.request'))
                            <a class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bt_login w-full flex justify-center items-center py-3">
                            Ingresar
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center text-sm text-gray-500">
                    <p>¿No tienes una cuenta? <a href="#" class="text-indigo-600 font-medium hover:text-indigo-800 transition-colors">Contáctanos</a></p>
                </div>
            </div>
        </div>
        
        <!-- Imagen lateral -->
        <div class="w-full lg:w-3/5 flex items-center justify-center p-8 bg-gradient-to-br from-indigo-600 to-purple-700 lg:rounded-l-3xl overflow-hidden">
            <div class="w-full max-w-2xl flex flex-col items-center justify-center text-center text-white p-8">
                <img src="/Images/ImgLogin.jpg" class="w-full max-w-md rounded-xl shadow-2xl mb-8" alt="Imagen de login"/>
            </div>
        </div>
    </div>
</body>
</html>