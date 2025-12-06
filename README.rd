Se esta utilizando Visual Studio Code como ide de desarrollo

con la extension Docker Registry Explorer y Docker Explorer para visualizacion y gestion de contenedores


Descargar el repositorio 

en WWW borrar y clonar 
git clone https://github.com/juanpabloanton/back-laravel-citas.git // este es el backend
git clone https://github.com/juanpabloanton/front-react-citas.git // frontend


colocar el .env de backend 

.env -> nombre del archivo 
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:179onZ4rwmG3BuYxLV3mrmWwNZiWMhDVM/Mq2DnsObw=
APP_DEBUG=true
APP_URL=http://localhost:91

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=172.17.0.1
DB_PORT=5432
DB_DATABASE=db_citas
DB_USERNAME=postgres
DB_PASSWORD=desarrollo

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
JWT_SECRET=6Gy3xzEuJNntmDB7cutzKfNBFwpoHfoZMRhQA174o4ndTbXRY6wRYvElySPDL6xZ


y para el frontend 

.env -> nombre del archivo
REACT_APP_URL= "http://localhost:91/api/"
REACT_APP_URL_ONLY="http://localhost:5000/"
REACT_APP_URL_IMAGE="http://localhost:5000/storage/"
REACT_APP_URL_MAINTENANCE=false
PORT=5000
DISABLE_ESLINT_PLUGIN=true

ambos colocarlos en la carpeta raiz del proyecto 


ejecutar docker-compose up -d

para la ejecucion de los contendores
Creating proyectocitas-master_system_machine_1 
Creating proyectocitas-master_adminer_1       
Creating postgres_16.8_desarrollo             
Creating pgadmin4_9.1.0_desarrollo      

donde las credenciales de la base de datos es 
Motor de base de datos: postgres
Usuario: postgres
Contraseña: desarrollo
Servidor: db 


Crear base de datos db_citas

Una vez creada la base de datos ejecutar los siguientes comandos dentro del contenedor proyectocitas-master_system_machine_1 click derecho ->attach visual studio code donde tenemos las herramientas necesarias 
para trabajar con laravel y react 

php artisan migrate
php artisan db:seed

ejecutara las tablas





