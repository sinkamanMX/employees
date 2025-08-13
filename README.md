# Dashboard de Gestión de Empleados

Un sistema completo de gestión y análisis de empleados desarrollado con Laravel 12, que incluye autenticación JWT, visualización de datos con gráficas interactivas y gestión completa de información de empleados.

##  Características Principales

- **Autenticación JWT**: Sistema seguro de login con tokens JWT
- **Dashboard**: Visualización de datos con gráficas interactivas
- **Filtros Avanzados**: Sistema de filtrado por ciudad, edad, género y educación
- **Tabla Interactiva**: Implementación con DataTables para mejor UX
- **Diseño Responsivo**: Interface adaptable usando TailwindCSS
- **Análisis de Datos**: Gráficas de distribución, correlaciones y predicciones

## Tecnologías Utilizadas

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: TailwindCSS (CDN), JavaScript Vanilla
- **Autenticación**: JWT (tymon/jwt-auth)
- **Base de Datos**: MySQL
- **Gráficas**: Chart.js
- **Tablas**: DataTables
- **HTTP Client**: Axios

## Requisitos del Sistema

- PHP >= 8.2
- Composer
- Node.js >= 16.x (opcional, para desarrollo)
- MySQL >= 8.0 o PostgreSQL >= 13
- Extensiones PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON

## 🔧 Instalación

### 1. Clonar el Repositorio

\`\`\`bash
git clone https://github.com/sinkamanMX/employees.git
cd employees
\`\`\`

### 2. Instalar Dependencias

\`\`\`bash
composer install
\`\`\`

### 3. Configurar Variables de Entorno

\`\`\`bash
cp .env.example .env
\`\`\`

Edita el archivo `.env` con tu configuración:

\`\`\`env
APP_NAME="Employee Dashboard"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employees_dashboard
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

JWT_SECRET=
JWT_TTL=60
JWT_REFRESH_TTL=20160
JWT_ALGO=HS256
\`\`\`

### 4. Generar Claves de Aplicación

\`\`\`bash
php artisan key:generate
\`\`\`

### 5. Instalar y Configurar JWT

\`\`\`bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
\`\`\`

### 6. Configurar Base de Datos

\`\`\`bash
# Crear la base de datos
mysql -u root -p -e "CREATE DATABASE employees_dashboard;"

# Ejecutar migraciones
php artisan migrate

# Poblar con datos de prueba
php artisan db:seed
\`\`\`

### 7. Iniciar el Servidor

\`\`\`bash
php artisan serve
\`\`\`

La aplicación estará disponible en: `http://127.0.0.1:8000`

##  Credenciales de Prueba

- **Email**: admin@empresa.com
- **Password**: 123456

## 📁 Estructura del Proyecto

\`\`\`
employee-dashboard/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   └── DashboardController.php
│   └── Models/
│       ├── Employee.php
│       └── User.php
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   └── js/
│       └── dashboard.js
├── resources/
│   └── views/
│       ├── auth/
│       ├── dashboard/
│       └── layouts/
├── routes/
│   ├── api.php
│   └── web.php
└── scripts/
    └── seed_employees.sql
\`\`\`

## Uso de la Aplicación

### 1. Acceso al Sistema
- Navega a la URL de la aplicación
- Inicia sesión con las credenciales proporcionadas

### 2. Dashboard de Indicadores
- **Tab "Indicadores"**: Visualiza gráficas analíticas
- Gráficas disponibles:
  - Distribución por género (dona)
  - Distribución por edad (barras)
  - Distribución por ciudad (barras)
  - Distribución por educación (barras)
  - Estado de bench (pastel)
  - Experiencia vs Nivel de pago (barras agrupadas)

### 3. Gestión de Empleados
- **Tab "Detalle"**: Gestión completa de empleados
- Panel de filtros por ciudad, edad, género y educación
- Tabla interactiva con DataTables
- Botón "Agregar Empleado" para nuevos registros

### 4. Funcionalidades Avanzadas
- Búsqueda en tiempo real en la tabla
- Paginación automática
- Ordenamiento por columnas
- Filtrado múltiple simultáneo

## Configuración Adicional

### Optimización para Producción

\`\`\`bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
\`\`\`


## Datos de Prueba

El sistema incluye un seeder con 100 empleados de prueba con datos realistas:
- Diferentes ciudades, edades y niveles educativos
- Variedad en experiencia y niveles de pago
- Estados de bench distribuidos
- Datos para predicción de abandono

## 👥 Autor

- **Tu Nombre** - *Desarrollo inicial* - [Enrique Peña](https://github.com/sinkamanMX)

