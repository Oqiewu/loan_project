# REST API для управления займами

## Функционал

API предоставляет следующие методы:

- POST /loans — создание нового займа.
- GET /loans/{id} — получение информации о займе.
- PUT /loans/{id} — обновление информации о займе.
- DELETE /loans/{id} — удаление займа.
- GET /loans — получение списка всех займов с базовыми фильтрами по дате создания и сумме.

## Запуск и использование API

### Локальная установка

1. Склонируйте репозиторий:

```
git clone https://github.com/Oqiewu/loan_project.git
```

2. Перейдите в директорию проекта:
```
cd loan_project
```

3. Установите зависимости с помощью Composer:
```
composer install
```

4. Запустите встроенный веб-сервер PHP:
```
php -S localhost:8000 -t public
```

### Развертывание с помощью Docker

1. Установите [Docker](https://docs.docker.com/get-docker/) и [Docker Compose](https://docs.docker.com/compose/install/).
2. Склонируйте репозиторий:
```
git clone git clone https://github.com/Oqiewu/loan_project.git
```
3. Перейдите в директорию docker:
```
cd loan_project/docker
```
4. Запустите контейнеры с помощью Docker Compose:
```
docker-compose up -d --build
```

API будет доступно на порту, указанном в файле docker-compose.yml или же "http://localhost"

## Тестирование

Для запуска тестов выполните следующую команду в директории проекта или внутри docker-контейнера php:
```
vendor/bin/phpunit
```
