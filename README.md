# GreenCycle

## Descripción

GreenCycle es una aplicación web desarrollada como proyecto académico que combina mecánicas de gestión virtual con concientización ambiental. Los usuarios pueden plantar y administrar árboles virtuales, monitorear su crecimiento, adquirir objetos para mejorar su desarrollo y gestionar recursos mediante un sistema de inventario.

El proyecto se desarrolla de forma incremental mediante sprints, incorporando nuevas funcionalidades en cada etapa del desarrollo.

---
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

---

Funcionalidades del Sprint 1

En esta primera entrega se definió la arquitectura inicial del sistema y el modelo de dominio. Las funcionalidades contempladas incluyen:

- Gestión de usuarios.
- Registro e inicio de sesión.
- Creación y consulta de árboles.
- Modelo de semillas.
- Diseño del sistema de inventario.
- Diseño del sistema de efectos.
- Definición de endpoints REST.
- Diseño de autorizaciones y control de acceso.

---

## Modelo de Dominio

### Entidades Principales

#### Usuario
Representa a la persona que utiliza la plataforma. Puede poseer árboles, administrar un inventario y realizar compras.

#### Árbol
Representa un árbol virtual asociado a un usuario. Mantiene información relacionada con salud, crecimiento y estado.

#### Semilla
Define el tipo de árbol que puede ser plantado.

#### Inventario
Almacena los ítems pertenecientes a un usuario.

#### Ítem
Objeto consumible que puede utilizarse para modificar el comportamiento de un árbol.

#### Efecto
Modificador temporal aplicado a un árbol mediante el uso de ítems.

---

## 🔌 API REST

### Autenticación

#### Registrar usuario

```http
POST /api/register
```

#### Iniciar sesión

```http
POST /api/login
```

#### Cerrar sesión

```http
POST /api/logout
```

### Árboles

#### Obtener todos los árboles del usuario autenticado

```http
GET /api/trees
```

#### Obtener un árbol específico

```http
GET /api/trees/{id}
```

#### Crear un árbol

```http
POST /api/trees
```

Ejemplo de solicitud:

```json
{
  "seed_id": 1
}
```

---

## Seguridad y Autorización

El sistema utiliza autenticación basada en tokens mediante Laravel Sanctum.

Cada usuario únicamente podrá acceder a los recursos que le pertenezcan:

- Consultar sus propios árboles.
- Crear árboles asociados a su cuenta.
- Modificar únicamente sus recursos.
- Bloquear el acceso a información de otros usuarios.

---

## Estructura General del Proyecto

```text
GreenCycle
│
├── backend
│   ├── app
│   ├── routes
│   ├── database
│   └── tests
│
├── frontend
│   ├── src
│   ├── components
│   ├── pages
│   └── services
│
└── README.md
```

---

## Evolución del Proyecto

### Sprint 1
- Modelo de dominio.
- Diseño de base de datos.
- Diseño de API REST.
- Definición de autenticación y autorización.

### Sprint 2
- Implementación del inventario.
- Sistema de tienda.
- Compra de ítems.
- Aplicación de efectos.

### Sprint 3
- Mecánicas de crecimiento.
- Gestión completa de estados de los árboles.
- Balance de progresión.
- Mejoras en experiencia de usuario.

---

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

## URL

 - Repositorio
```https://github.com/AllanCRC5/Proyecto_GreenCycle_AJV.git
```

 - Neon

```postgresql://neondb_owner:npg_hZ6SbH5tRzsY@ep-morning-feather-aeoaz4py-pooler.c-2.us-east-2.aws.neon.tech/neondb?sslmode=require&channel_binding=require
```

## Equipo de Desarrollo

- Valeria Leticia Salas Jiménez
- Allan Castro Rodríguez
- Jean Marco Escobar Rojas

---

## Licencia

Este proyecto fue desarrollado con fines académicos.

<!-- branch protection test -->
