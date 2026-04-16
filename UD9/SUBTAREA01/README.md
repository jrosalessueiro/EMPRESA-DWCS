# JoseRosales Sueiro - DWCS 2026

# Subtarea 01: Registro de Usuarios con Symfony

Este proyecto implementa un sistema de registro básico siguiendo la arquitectura del framework Symfony.

## Tecnologías utilizadas

- **Framework:** Symfony 6.x / 7.x
- **Base de Datos:** SQLite (archivo `var/data.db`)
- **ORM:** Doctrine

## Pasos para ejecutar el proyecto

1. Instalar dependencias: `composer install`
2. Ejecutar migraciones: `php bin/console doctrine:migrations:migrate`
3. Iniciar servidor: `symfony serve` o `php -S localhost:8000 -t public`

## Rutas principales

- **/register**: Formulario de registro de nuevos usuarios.
