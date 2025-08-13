@extends('layouts.app')

@section('title', 'Dashboard de Empleados')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar text-2xl text-indigo-600 mr-3"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Empleados</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span id="userName" class="text-gray-700"></span>
                    <button onclick="logout()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Tabs Navigation -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="switchTab('indicadores')" id="tab-indicadores" class="tab-button active border-b-2 border-indigo-500 py-2 px-1 text-sm font-medium text-indigo-600">
                        <i class="fas fa-chart-bar mr-2"></i>Indicadores
                    </button>
                    <button onclick="switchTab('detalle')" id="tab-detalle" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-table mr-2"></i>Detalle
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content: Indicadores -->
        <div id="content-indicadores" class="tab-content">
            <!-- Action Button -->
            <div class="mb-6 flex justify-end">
                <button onclick="refreshDashboard()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-sync-alt mr-2"></i>Actualizar
                </button>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Gender Distribution -->
                <div class="bg-white rounded-lg shadow card-shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-venus-mars text-pink-500 mr-2"></i>Distribución por Género
                    </h3>
                    <div class="chart-container">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>

                <!-- Age Distribution -->
                <div class="bg-white rounded-lg shadow card-shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-birthday-cake text-orange-500 mr-2"></i>Distribución por Edad
                    </h3>
                    <div class="chart-container">
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>

                <!-- City Distribution -->
                <div class="bg-white rounded-lg shadow card-shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>Distribución por Ciudad
                    </h3>
                    <div class="chart-container">
                        <canvas id="cityChart"></canvas>
                    </div>
                </div>

                <!-- Education Distribution -->
                <div class="bg-white rounded-lg shadow card-shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-graduation-cap text-purple-500 mr-2"></i>Distribución por Educación
                    </h3>
                    <div class="chart-container">
                        <canvas id="educationChart"></canvas>
                    </div>
                </div>

                <!-- Experience vs Payment -->
                <div class="bg-white rounded-lg shadow card-shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-chart-line text-green-500 mr-2"></i>Experiencia vs Nivel de Pago
                    </h3>
                    <div class="chart-container">
                        <canvas id="experiencePayChart"></canvas>
                    </div>
                </div>

                <!-- Benched History -->
                <div class="bg-white rounded-lg shadow card-shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-clock text-yellow-500 mr-2"></i>Historial de Bench
                    </h3>
                    <div class="chart-container">
                        <canvas id="benchedChart"></canvas>
                    </div>
                </div>

                <!-- Leave Prediction -->
                <div class="bg-white rounded-lg shadow card-shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Predicción de Abandono
                    </h3>
                    <div class="chart-container">
                        <canvas id="leaveChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Detalle -->
        <div id="content-detalle" class="tab-content hidden">
            <!-- Panel de filtros -->
            <div class="bg-white rounded-lg shadow card-shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-filter text-blue-500 mr-2"></i>Filtros
                </h3>
                <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad</label>
                        <select name="city" id="filter-city" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Todas las ciudades</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Edad</label>
                        <select name="age" id="filter-age" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Todas las edades</option>
                            <option value="20-25">20-25 años</option>
                            <option value="26-30">26-30 años</option>
                            <option value="31-35">31-35 años</option>
                            <option value="36-40">36-40 años</option>
                            <option value="41+">41+ años</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Género</label>
                        <select name="gender" id="filter-gender" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Todos los géneros</option>
                            <option value="Male">Masculino</option>
                            <option value="Female">Femenino</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Educación</label>
                        <select name="education" id="filter-education" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Todas las educaciones</option>
                        </select>
                    </div>
                </form>
                <div class="mt-4 flex justify-end space-x-3">
                    <button onclick="clearFilters()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </button>
                    <button onclick="applyFilters()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-200">
                        <i class="fas fa-search mr-2"></i>Aplicar Filtros
                    </button>
                </div>
            </div>

            <!-- Tabla de empleados con DataTables -->
            <div class="bg-white rounded-lg shadow card-shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-users text-green-500 mr-2"></i>Lista de Empleados
                        </h3>
                        <!-- Agregando botón Agregar Empleado -->
                        <button id="addEmployeeButton" onclick="openAddEmployeeModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200">
                            <i class="fas fa-plus mr-2"></i>Agregar Empleado
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <table id="employeesTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Educación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año Ingreso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel Pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Género</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bench</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experiencia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Abandono</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Los datos se cargarán aquí via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para agregar empleado -->
    <div id="addEmployeeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user-plus text-green-500 mr-2"></i>Agregar Nuevo Empleado
                    </h3>
                    <button onclick="closeAddEmployeeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="addEmployeeForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Educación *</label>
                            <select name="education" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Seleccionar educación</option>
                                <option value="Bachelor's">Bachelor's</option>
                                <option value="Master's">Master's</option>
                                <option value="PhD">PhD</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Año de Ingreso *</label>
                            <input type="number" name="joining_year" required min="2000" max="2024" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad *</label>
                            <input type="text" name="city" required 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nivel de Pago *</label>
                            <select name="payment_tier" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Seleccionar nivel</option>
                                <option value="1">Tier 1</option>
                                <option value="2">Tier 2</option>
                                <option value="3">Tier 3</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Edad *</label>
                            <input type="number" name="age" required min="18" max="70" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Género *</label>
                            <select name="gender" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Seleccionar género</option>
                                <option value="Male">Masculino</option>
                                <option value="Female">Femenino</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">¿Ha estado en Bench?</label>
                            <select name="ever_benched" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="No">No</option>
                                <option value="Yes">Sí</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Experiencia en Dominio Actual *</label>
                            <input type="number" name="experience_in_current_domain" required min="0" max="50" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">¿Planea dejar la empresa?</label>
                            <select name="leave_or_not" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeAddEmployeeModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-200">
                            <i class="fas fa-save mr-2"></i>Guardar Empleado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
