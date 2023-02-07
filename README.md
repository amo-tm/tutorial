### Примеры интеграции с amo | корпоративный мессенджер

Материал к документации http://developers.amo.tm/docs/

## Туториалы
- Быстрый старт - https://github.com/amo-tm/tutorial/tree/bootstrap
- Разработка директ-бота - https://github.com/amo-tm/tutorial/tree/tutorial-1
- Встраивание чата в собственный продукт (closed beta) - https://github.com/amo-tm/tutorial/tree/tutorial-4

## Тестирование

1. Установить зависимости

```shell
composer install -o 
```

2. На портале разработчика https://developers.amo.tm создать приложение и прописать в ENV переменные CLIENT_ID, CLIENT_SECRET

```shell
CLIENT_ID='your app id' CLIENT_SECRET='your app secret' php -S 127.0.0.1:8080 -t .
```

3. Запустить ngrok

```shell
ngrok http 8080
```

4. Прописать в настройках приложения на портале https://developers.amo.tm 
   - OAuth redirect - [ngrok-domain]/amo_authorization.php
