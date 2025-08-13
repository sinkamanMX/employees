@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="min-h-screen flex items-center justify-center gradient-bg py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <div class="text-center">
                <i class="fas fa-users text-4xl text-indigo-600 mb-4"></i>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Bienvenido</h2>
                <p class="text-gray-600">Ingresa tus credenciales para continuar</p>
            </div>
            
            <form id="loginForm" class="mt-8 space-y-6">
                <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"></div>
                
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="admin@empresa.com">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <input id="password" name="password" type="password" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" id="loginButton"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                    <span id="loginText">Iniciar Sesión</span>
                    <i id="loginSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Credenciales de prueba:<br>
                    <strong>Email:</strong> admin@sistema.com<br>
                    <strong>Contraseña:</strong> 123456
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const loginButton = document.getElementById('loginButton');
    const loginText = document.getElementById('loginText');
    const loginSpinner = document.getElementById('loginSpinner');
    const errorMessage = document.getElementById('errorMessage');
    
    loginButton.disabled = true;
    loginText.textContent = 'Iniciando sesión...';
    loginSpinner.classList.remove('hidden');
    errorMessage.classList.add('hidden');
    
    const formData = new FormData(this);
    const data = {
        email: formData.get('email'),
        password: formData.get('password')
    };
    
    try {
        const response = await axios.post('/auth/login', data);
        
        if (response.data.success) {
            localStorage.setItem('jwt_token', response.data.token);
            localStorage.setItem('user_data', JSON.stringify(response.data.user));
            
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 100);
        }
    } catch (error) {
        console.error('Error de login:', error);
        errorMessage.textContent = error.response?.data?.message || 'Error al iniciar sesión';
        errorMessage.classList.remove('hidden');
    } finally {
        loginButton.disabled = false;
        loginText.textContent = 'Iniciar Sesión';
        loginSpinner.classList.add('hidden');
    }
});

if (localStorage.getItem('jwt_token')) {
    window.location.href = '/dashboard';
}
</script>
@endsection
