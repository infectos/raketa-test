- Структура файлов неверная. Исправлено по DDD.
- Присутствуют уязвимости sql инъекций. Заменить на Prepared Statements.
- Корзина не сохраняется после добавления позиции.
- GetCartController всегда возвращает 404
- Неправильная логика подсчета total у item'а в cartView. Счетчик не сбрасывается.
- Нет данных о Customer, когда создается Корзина
- Опечатки в getProductsController (productsVew)
- В миграции id не имеет смысла, если есть uuid
- В миграции тип для uuid должен быть binary(16)
- В миграции тип для is_active должен быть boolean либо tinyint(1)
- В миграции тип для price должен быть int. Хорошо бы для цен еще поле currency добавить.
