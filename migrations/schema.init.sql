create table if not exists products
(
    uuid  BINARY(16) not null primary key comment 'UUID товара',
    category  varchar(255) not null comment 'Категория товара',
    is_active tinyint(1) default 1  not null comment 'Флаг активности',
    name text default '' not null comment 'Тип услуги',
    description text null comment 'Описание товара',
    thumbnail  varchar(255) null comment 'Ссылка на картинку',
    price int not null comment 'Цена'
)
    comment 'Товары';

create index is_active_idx on products (is_active);
