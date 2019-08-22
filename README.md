**Вимоги**

1. Мінімальна версія php 7.1
2. MySQL 5.6
3. Composer

**Розгортання проекту:**

1. Стягнути репозиторій
2. Внести дані доступу до БД в .env файл
3. Composer update
4. php bin/console doctrine:migrations:migrate
  
запустити сервер

```
 php bin/console server:run
```
