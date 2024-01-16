# Instalación

## Docker

Editar el archivo **.env.prod**
```.env
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=ExampleSecret
APP_URL_STRING=http://localhost #Esto es simplemente para mostrar en los resultados de la API
```
**Ejecutar el comando**
```bash
docker-compose up -d
```

Para detener el contenedor simplemente usar
```bash
docker-compose down
```

## Apache

- Renombrar el archivo **.env.prod** a **.env**
- Editar el archivo .env
```.env
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=ExampleSecret
APP_URL_STRING=http://localhost #Esto es simplemente para mostrar en los resultados de la API
```

- Ejecutar los comandos
```bash
composer install --no-dev --optimize-autoloader
composer dump-env prod
```
- Crear el VirtualHost usando
```conf
<VirtualHost *:80>

	DocumentRoot "/var/www/swapi/public" #ruta de ejemplo

	ServerName starwars.server #DNS de ejemplo IMPORTANTE! 

	<Directory /var/www/swapi/public>

		AllowOverride None

		Require all granted

		FallbackResource /index.php

	</Directory>

</VirtualHost>
```
- Reiniciar el servidor Apache