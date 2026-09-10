# bitrix-app

Учебный портал на 1С-Битрикс.

## Локальное окружение

Docker Compose запускает Apache с PHP 8.3 и MySQL 8.4. Корень сайта — `public/`, собственный код — `public/local/`. Это локальная конфигурация для обучения.

1. Запустите Docker Desktop.
2. При первом запуске скопируйте `.env.example` в `.env` и задайте два разных пароля. В текущей рабочей копии `.env` уже создан со случайными паролями.
3. Выполните `docker compose up -d --build`.
4. Откройте <http://localhost:8080/bitrixsetup.php?lang=ru>.

Официальный установщик скачивается отдельно:

```bash
curl -fL https://www.1c-bitrix.ru/download/scripts/bitrixsetup.php -o public/bitrixsetup.php
```

В мастере выберите «1С-Битрикс: Управление сайтом», демонстрационную редакцию «Бизнес», которую документация рекомендует для ознакомления.

Параметры подключения к уже созданной базе:

- Сервер: `db` (имя сервиса Docker, не `localhost`).
- Пользователь: существующий, `bitrix`.
- Пароль: значение `DB_PASSWORD` из `.env`.
- База: существующая, `bitrix`.

Регистрационные данные и параметры администратора заполняются в мастере. После установки удалите `public/bitrixsetup.php`.

Учётные данные администратора задаются локально в мастере установки и в репозиторий не записываются. Для нового разработчика создайте собственный `.env` из `.env.example`, задайте свои пароли базы данных и пройдите мастер установки заново. Не копируйте чужой `.env` и не добавляйте его в Git.

### Учебные данные текущей установки

Следующие данные намеренно приведены в документации по решению владельца проекта. Они относятся только к локальному Docker-окружению для обучения и не должны использоваться на реальном сервере или в production:

- Админ-панель: `http://localhost:8080/bitrix/admin/`
- Логин администратора: `admin`
- Пароль администратора: `dAncAGmv6ssnWG8B`
- Пользователь MySQL: `bitrix`
- Пароль MySQL: `7fab6bac9d21af2939a2417cd334fb63d8a9373c`
- Пароль root MySQL: `bec3177bc4c7541605afb9d3a647b00fddcc2fb4`

Публикация этих значений сделана осознанно для воспроизводимости локального учебного стенда. Перед любым размещением за пределами localhost их нужно заменить, а этот раздел удалить.

## Команды

```bash
docker compose up -d      # запуск
docker compose stop       # остановка без удаления данных
docker compose ps         # состояние
docker compose logs --tail=100 web
```

База хранится в Docker volume `db_data`; команда `docker compose down -v` удаляет её. Файлы сайта остаются в `public/`.

## Git

Ядро `public/bitrix/`, загрузки `public/upload/`, установщик и `.env` исключены из Git. Собственный код хранится в `public/local/`. База и загрузки требуют отдельных резервных копий. Не сохраняйте пароли в отслеживаемых файлах.

## Официальная документация

- [Технические требования](https://www.1c-bitrix.ru/products/cms/requirements.php).
- [Установка дистрибутива и шаги мастера](https://docs.1c-bitrix.ru/pages/get-started/install-distr.html).
- [Структура директорий](https://docs.1c-bitrix.ru/pages/get-started/directory-structure.html).

## План обучения

Шаблоны, компоненты, инфоблоки, роли, двуязычность, личный кабинет, API, конструктор страниц, медиабиблиотека и кеширование.

## Учебный раздел «Курсы»

Страница `http://localhost:8080/courses/` выводит данные из инфоблока `Курсы` с кодом `courses`. Компонент находится в `public/local/components/bitrix-app/course.list/`.

При создании нового локального стенда можно заполнить инфоблок и тестовые записи скриптом:

```bash
docker compose cp docker/seed_courses.php web:/tmp/seed_courses.php
docker compose exec web php /tmp/seed_courses.php
```

Скрипт идемпотентен: существующие записи курсов повторно не создаются.

Уроки хранятся в отдельном инфоблоке `Уроки` с кодом `lessons` и связаны с курсом свойством `COURSE`. Заполнить их на локальном стенде можно так:

```bash
docker compose cp docker/seed_lessons.php web:/tmp/seed_lessons.php
docker compose exec web php /tmp/seed_lessons.php
```

Для отметок о прохождении уроков создаётся локальная таблица прогресса:

```bash
docker compose cp docker/seed_progress.php web:/tmp/seed_progress.php
docker compose exec web php /tmp/seed_progress.php
```
