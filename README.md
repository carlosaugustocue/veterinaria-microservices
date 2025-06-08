# Veterinaria Microservices

## Descripción del Proyecto
Sistema de gestión veterinaria basado en microservicios, diseñado para administrar de manera eficiente las operaciones de una clínica veterinaria o hospital veterinario.

## Estructura del Proyecto
El proyecto está organizado en varios microservicios independientes que trabajan en conjunto:

- **auth-service**: Servicio de autenticación y autorización
- **pets-service**: Servicio de gestión de mascotas
- Otros servicios (en desarrollo)

### Tecnologías Utilizadas
- PHP
- MySQL
- XAMPP como entorno de desarrollo local
- Arquitectura de microservicios

## Requisitos de Instalación
### Prerrequisitos
- XAMPP (Apache, MySQL, PHP)
- PHP 7.4 o superior
- Acceso a MySQL

### Configuración de Base de Datos
1. El proyecto utiliza las siguientes bases de datos:
   - `veterinaria_auth`: Base de datos para la autenticación de usuarios (UTF8MB4, collation utf8mb4_unicode_ci)
   - `veterinaria_pets`: Base de datos para la gestión de mascotas (UTF8MB4, collation utf8mb4_unicode_ci)

## Servicios Disponibles

### Servicio de Autenticación (auth-service)
Maneja la autenticación y autorización de usuarios dentro del sistema.

#### Funcionalidades:
- Registro de usuarios
- Inicio de sesión
- Gestión de permisos
- Tokens de autenticación

### Servicio de Mascotas (pets-service)
Gestiona la información relacionada con las mascotas registradas en el sistema.

#### Funcionalidades:
- Registro de mascotas
- Historial médico
- Información de propietarios
- Recordatorios de citas y vacunas
## Instalación y Configuración

1. Clonar el repositorio:
   ```
   git clone [URL_DEL_REPOSITORIO]
   ```

2. Configurar las bases de datos:
   ```sql
   CREATE DATABASE veterinaria_auth CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE DATABASE veterinaria_pets CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Configurar cada servicio según las instrucciones específicas en sus respectivos directorios.

## Desarrollo

### Guías de Contribución
1. Cada microservicio debe ser independiente y comunicarse a través de APIs.
2. Seguir las convenciones de nomenclatura establecidas.
3. Documentar adecuadamente las APIs y el código.

## Estado del Proyecto
En desarrollo inicial. Actualmente implementando los servicios de autenticación y de mascotas.

## Contacto
[Información de contacto del equipo de desarrollo]

