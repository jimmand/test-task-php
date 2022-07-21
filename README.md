# Тестовое задание
Выгрузка сделок в БД

### Стек
* Laravel
* php
* docker
* postgresql


### Использование
Запустите скрипт (установит пакеты и запустит docker)
```shell
source prepare.sh
```

Добавьте в конец `.env`
```dotenv
CLIENT_ID=
SECRET=
DOMAIN=
ACCESS_TOKEN=
```

Замените секцию с настройкой БД (подключается к докеру)
```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5400
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=secret
```

Миграции
```shell
php artisan migrate
```

Делаем выгрузку
```shell
php artisan leads:dump
```

