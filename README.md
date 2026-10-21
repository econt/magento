# 📦 Econt Magento 2 Модул
## Официална Интеграция за Доставки

## Таблица с поддържаните версии на Magento 2 и PHP
| Magento 2| PHP |
|-------------------|-------------------------------------|
| 2.4.2 | 7.4 |
| 2.4.3 | 7.4, 8.0 |
| 2.4.4 | 8.1 |
| 2.4.5 | 8.1 |
| 2.4.6 | 8.1, 8.2| 
| 2.4.7 | 8.2, 8.3 |
| 2.4.8 | 8.3, 8.4 |

Версии преди 2.4.2 не се поддържат поради спряна поддръжка на Composer 1. Възможно е модула да работи и 2.4.1 и 2.4.0, но ще трябва да качите кода в app/code.

Поддръжката на 2.4.2 и 2.4.3 е добавена във версия 1.0.8 на модула.

## Инсталация

### През Composer

```bash
composer require econt-express/magento2-delivery
bin/magento setup:upgrade
```

### През архив

Свалете „Source code (zip)“ от [последния release](https://github.com/econt/magento/releases/latest) и разархивирайте съдържанието в `app/code/Oxl/Delivery`. После изпълнете `bin/magento setup:upgrade` в главната директория на Magento.

Подробно: [Инсталация](https://github.com/econt/magento/wiki/Инсталация) в wiki-то.

## Лиценз

MIT
