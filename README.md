# Utilify

### Prerequisites

```
PHP >= 8.3
Composer
```

### Installing

```shell
$ git clone git@github.com:omnitech-solutions/utilify-php.git
$ cd utilify-php

$ composer install
$ pecl install xdebug # Installs lib under homebrew cellar e.g. /opt/homebrew/Cellar/php/8.3.11/pecl/20230831/xdebug.so
```

### Configure XDebug

Print php config file
```shell
$ php --ini # prints php config file location e.g. /opt/homebrew/etc/php/8.3/php.ini
```

### Scripts

Edit php.ini and include `xdebug.mode` under `zend_extension`
```ini
xdebug.mode=coverage
```

🧹 Keep a modern codebase with **Pint**:
```bash
composer lint
```

✅ Run refactors using **Rector**
```bash
composer refacto
```

⚗️ Run static analysis using **PHPStan**:
```bash
composer test:types
```

✅ Run unit tests using **PEST**
```bash
composer test:unit
```

🚀 Run the entire test suite:
```bash
composer test
```

**Utilify PHP** was created by **Desmond O'Leary** under the **[MIT license](https://opensource.org/licenses/MIT)**.
