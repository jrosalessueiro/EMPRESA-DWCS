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

1. La Vista (Frontend): El usuario llega a la ruta /register. El servidor le sirve un formulario HTML (creado con Twig(Symfony) o Blade(Laravel).
2. Validación: Cuando el usuario rellena los campos (Email, Password) y da a enviar, el Controlador encomprueba si el email es real real y si la contraseña es suficientemente larga.
3. Hasing (Seguridad): La contraseña se guarda usando un algoritmo de cifrado para convertirla en una cadena ilegible.
4. Escritura en BD (ORM): El ORM (Doctrine en Symfony o Eloquent en Laravel) traduce el objeto "Usuario" a una sentencia SQL:INSERT INTO users....
5. Persistencia: Los datos se escriben físicamente en un archivo SQLite.
