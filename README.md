# Web Development Laravel Starter

Proyecto base para el curso **TM4100**, preparado para desarrollar aplicaciones web con Laravel en un entorno reproducible.

## Propósito

Esta aplicación proporciona un entorno inicial para aprender y practicar:

- Arquitectura MVC.
- Rutas y controladores.
- Vistas con Blade.
- Formularios y validación de datos.
- Eloquent ORM.
- Migraciones y seeders.
- Integración con PostgreSQL.
- Pruebas automatizadas.
- Control de versiones con Git y GitHub.
- Integración continua con GitHub Actions.
- Despliegue de aplicaciones web.

## Tecnologías

El proyecto utiliza las siguientes tecnologías:

- PHP 8.5.
- Laravel 13.
- Laravel Herd.
- Blade.
- CSS.
- JavaScript.
- Vite.
- PostgreSQL en Neon.
- PHPUnit.
- Laravel Pint.
- GitHub Actions.
- Render.

## Requisitos locales

Antes de instalar el proyecto, asegúrese de contar con:

- Windows 10 o una versión posterior.
- Laravel Herd.
- PHP 8.5.
- Composer 2.
- Node.js 26.
- npm.
- Git.
- Visual Studio Code.
- Una cuenta de Neon.
- Una cuenta de Render.

> No es necesario instalar Docker ni PostgreSQL localmente.

## Instalación local

### 1. Clonar el repositorio

Abra PowerShell y ejecute:

```powershell
git clone URL_DEL_REPOSITORIO
Set-Location webdev-laravel-starter
```

### 2. Aplicar la configuración de Laravel Herd

```powershell
herd init
herd isolate 8.5
```

### 3. Seleccionar la versión de Node.js

```powershell
nvm use
```

> Este comando utiliza la versión de Node.js definida para el proyecto.

### 4. Instalar las dependencias

Instale las dependencias de PHP:

```powershell
composer install
```

Instale las dependencias de frontend:

```powershell
npm ci
```

### 5. Crear la configuración local

Copie el archivo de configuración de ejemplo:

```powershell
Copy-Item .env.example .env
```

Genere la clave de la aplicación:

```powershell
php artisan key:generate
```

### 6. Configurar la base de datos

Abra el archivo `.env` y agregue la conexión correspondiente a la rama `development` de Neon:

```dotenv
DB_CONNECTION=pgsql
DB_URL="URL_DE_NEON_DEVELOPMENT"
DB_SSLMODE=require
```

> Sustituya `URL_DE_NEON_DEVELOPMENT` por la cadena de conexión proporcionada por Neon.

### 7. Limpiar la configuración almacenada

```powershell
php artisan optimize:clear
```

### 8. Ejecutar las migraciones y seeders

```powershell
php artisan migrate --seed
```

### 9. Iniciar Vite

```powershell
npm run dev
```

Mantenga esta terminal abierta mientras desarrolla la aplicación.

### 10. Abrir la aplicación

Visite la siguiente dirección en el navegador:

```text
http://webdev-laravel-starter.test
```

## Comprobaciones del proyecto

Antes de crear un commit o un pull request, ejecute las siguientes comprobaciones.

### Ejecutar las pruebas automatizadas

```powershell
php artisan test
```

### Comprobar el formato del código PHP

```powershell
.\vendor\bin\pint --test
```

Para corregir automáticamente el formato:

```powershell
.\vendor\bin\pint
```

### Crear los recursos de producción

```powershell
npm run build
```

## Ambientes

| Ambiente | Aplicación | Base de datos |
|---|---|---|
| Desarrollo | Laravel Herd | Neon `development` |
| Pruebas | PHPUnit | SQLite en memoria |
| Producción | Render | Neon `production` |

## Seguridad

Nunca deben publicarse o incluirse en el repositorio:

- El archivo `.env`.
- Las credenciales de Neon.
- El valor de `APP_KEY`.
- Tokens de GitHub.
- Contraseñas.
- Claves de API.
- Archivos que contengan información privada.
- Cadenas de conexión de bases de datos.

Antes de realizar un commit, revise los archivos modificados:

```powershell
git status
```

## Flujo de trabajo

El flujo recomendado para desarrollar una funcionalidad es el siguiente:

1. Actualizar la rama `main`.
2. Crear una nueva rama de trabajo.
3. Desarrollar la funcionalidad.
4. Ejecutar las pruebas localmente.
5. Comprobar el formato del código.
6. Crear el commit.
7. Publicar la rama en GitHub.
8. Crear un pull request.
9. Esperar las comprobaciones automáticas.
10. Revisar el código.
11. Fusionar el pull request mediante **squash**.
12. Actualizar el repositorio local.

### Actualizar la rama principal

```powershell
git switch main
git pull origin main
```

### Crear una rama de trabajo

```powershell
git switch -c feature/nombre-de-la-funcionalidad
```

### Guardar los cambios

```powershell
git add .
git commit -m "feat: agregar nombre de la funcionalidad"
```

### Publicar la rama

```powershell
git push -u origin feature/nombre-de-la-funcionalidad
```

### Actualizar el repositorio después de fusionar

```powershell
git switch main
git pull origin main
```

## Configuración pendiente

Antes de distribuir el proyecto, sustituya:

```text
URL_DEL_REPOSITORIO
```

por la URL real del repositorio en GitHub.

También debe sustituir:

```text
URL_DE_NEON_DEVELOPMENT
```

por la cadena de conexión de la base de datos de desarrollo en Neon.


## Estado del proyecto
El repositorio utiliza GitHub Actions para validar pruebas, formato y recursos frontend.
=======
<!-- branch protection test -->
 

## GreenCycle — Sprint 2

### Descripción

GreenCycle es una aplicación desarrollada con Laravel que permite a los usuarios gestionar árboles virtuales. Cada árbol tiene un tipo de semilla, nivel, salud, progreso y estado.

Durante el Sprint 2 se implementó la funcionalidad principal para:

- Consultar los árboles de un usuario.
- Plantar árboles.
- Consultar un árbol específico.
- Cuidar árboles.
- Controlar los tiempos de espera entre cuidados.
- Aplicar el deterioro automático de la salud.
- Cambiar el estado de los árboles según su salud y progreso.
- Gestionar los límites de árboles activos por usuario.

### Configuración de GreenCycle

Los parámetros del sistema se encuentran centralizados en:

```text
config/greencycle.php
```

Los valores predeterminados son:

| Parámetro | Valor predeterminado | Descripción |
|---|---:|---|
| `max_active_trees` | 5 | Máximo de árboles activos por usuario. |
| `max_level` | 10 | Nivel máximo de un árbol. |
| `health_initial` | 100 | Salud inicial de un árbol. |
| `health_max` | 100 | Salud máxima de un árbol. |
| `care_heal` | 20 | Salud recuperada al cuidar un árbol. |
| `progress_initial` | 0 | Progreso inicial del árbol. |
| `care_cooldown_minutes` | 60 | Minutos de espera entre cuidados. |
| `decay_interval_minutes` | 1440 | Intervalo de deterioro de la salud. |
| `decay_amount` | 20 | Salud perdida por cada intervalo de deterioro. |

Estos valores pueden configurarse mediante variables de entorno en el archivo `.env`.

Ejemplo:

```dotenv
GREENCYCLE_MAX_ACTIVE_TREES=5
GREENCYCLE_MAX_LEVEL=10
GREENCYCLE_HEALTH_INITIAL=100
GREENCYCLE_HEALTH_MAX=100
GREENCYCLE_CARE_HEAL=20
GREENCYCLE_PROGRESS_INITIAL=0
GREENCYCLE_CARE_COOLDOWN_MINUTES=60
GREENCYCLE_DECAY_INTERVAL_MINUTES=1440
GREENCYCLE_DECAY_AMOUNT=20
```

Después de modificar la configuración, se recomienda limpiar la configuración almacenada:

```bash
php artisan optimize:clear
```

### API de GreenCycle

Las rutas de la API utilizan el prefijo:

```text
/api/v1
```

Las operaciones relacionadas con árboles requieren autenticación mediante Laravel Sanctum.

| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/v1/trees` | Consultar los árboles del usuario autenticado. |
| POST | `/api/v1/trees` | Plantar un árbol. |
| GET | `/api/v1/trees/{tree}` | Consultar un árbol específico. |
| POST | `/api/v1/trees/{tree}/care` | Cuidar un árbol. |

Las respuestas de la API utilizan recursos de Laravel para estructurar los datos y mantener un formato consistente.

### Estados de los árboles

Los árboles pueden presentar los siguientes estados:

| Estado | Descripción |
|---|---|
| `ACTIVE` | El árbol puede recibir cuidados y continuar creciendo. |
| `MATURE` | El árbol alcanzó el nivel máximo. |
| `DEAD` | La salud del árbol llegó a cero. |
| `HARVESTED` | El árbol fue cosechado. |

### Decaimiento automático

El deterioro de la salud de los árboles se ejecuta mediante el comando:

```bash
php artisan trees:apply-decay
```

El comando revisa los árboles activos y aplica el deterioro correspondiente según el tiempo transcurrido.

La tarea está registrada en el programador de Laravel para ejecutarse cada minuto:

Schedule::command('trees:apply-decay')
    ->everyMinute()
    ->withoutOverlapping();

Para ejecutar el programador durante el desarrollo local:

php artisan schedule:work

### Pruebas de GreenCycle

Para ejecutar las pruebas automatizadas:


php artisan test

Las pruebas verifican el funcionamiento de las operaciones principales, las reglas de negocio, la autenticación y los estados de los árboles.

### Alcance del Sprint 2

El Sprint 2 se enfoca en la gestión de árboles, sus cuidados, crecimiento, salud y deterioro automático.
 
##Funcionamiento

Para revisar el funcionamiento de manera local se deben seguir las siguientes instrucciones:
## Instalación y ejecución local

### Requisitos

Antes de comenzar, asegúrate de tener instalado:

- PHP 8.3 o superior.
- Composer 2.
- Node.js 26 y npm.
- PostgreSQL.
- Git.

### 1. Clonar el repositorio

```bash
git clone URL_DEL_REPOSITORIO
cd Proyecto_GreenCycle_AJV
```

### 2. Instalar dependencias

```bash
composer install
npm ci
```

### 2.1(Opcional, en mi caso fue necesario debido a que no tenía acceso al documento). Configurar el entorno

Crear el archivo `.env` a partir del ejemplo:

```bash
cp .env.example .env
```

Generar la clave de Laravel:

```bash
php artisan key:generate
```

### 3. Configurar la base de datos

Crear una base de datos PostgreSQL llamada `greencycle`:

```bash
createdb greencycle
```

Configurar las credenciales de PostgreSQL en el archivo `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=greencycle
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 4. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

Esto crea las tablas e inserta los datos de demostración.

### 5. Iniciar el proyecto

Abre dos terminales dentro de la carpeta del proyecto.

**Terminal 1 — Laravel:**

```bash
php artisan serve
```

**Terminal 2 — Vite:**

```bash
npm run dev
```

Abre la dirección que muestra Laravel en la terminal, normalmente:

```text
http://127.0.0.1:8000
```

### 6. Ejecutar las pruebas

Para comprobar el funcionamiento del proyecto:

```bash
php artisan test
```

Para verificar el formato del código:

```bash
vendor/bin/pint --test
```

### Comando de deterioro automático

Para ejecutar manualmente el deterioro de los árboles:

```bash
php artisan trees:apply-decay
```

Para mantener activo el programador de tareas durante el desarrollo:

```bash
php artisan schedule:work
```

**Nota:** No compartas tu archivo `.env` ni publiques tus credenciales de base de datos.

