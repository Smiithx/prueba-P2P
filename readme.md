## Requerimientos

- PHP >= 7.0
- Composer.

## Instalación

Instalar las dependencias del proyecto con el comando

```
composer install
```

Copiar y configurar el archivo de configuracion .env

```
cp .env.example .env
```

Instalar la key de la aplicacion

```
php artisan key:generate
```

Crear la base de datos y ejecutar las migraciones

```
php artisan migrate
```

## Uso

Ejecutar la aplicación a traves del comando.

```
 php artisan serve
```
   
Ingresar a la ruta `/payments` para ver el listado de pagos con sus transacciones
 
Ingresar a la ruta `/payments/create` para registar un nuevo pago

## Test

Para ejecutar las pruebas unitarias, ejecute el comando en la consola

```
./vendor/bin/phpunit
```