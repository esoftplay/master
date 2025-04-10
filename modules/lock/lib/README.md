### Requirements

Cache libraries dibangun dengan menggunakan versi PHP detail berikut ini, jika menggunakan versi yang lain belum dilakukan UnitTest yang ideal untuk memastikan library ini berjalan dengan baik:

```sh
PHP 8.1.5 (cli) (built: Apr 16 2022 00:03:52) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.1.5, Copyright (c) Zend Technologies
    with Xdebug v3.1.4, Copyright (c) 2002-2022, by Derick Rethans
    with Zend OPcache v8.1.5, Copyright (c), by Zend Technologies
```

### Pengaturan

Ada 2 metode yang dapat dilakukan untuk melakukan pengaturan library, pertama menggunakan environment variable. Kamu dapat menggunakan thirdparty untuk load environment dari file `.env` semisal [PHP dotenv](https://github.com/vlucas/phpdotenv).

| #Key           | #Type        | #Value                          | #Default  | #Required |
| -------------- | ------------ | ------------------------------- | --------- | --------- |
| CACHE_PROVIDER | string       | memcached / redis               | memcached | yes       |
| CACHE_HOST     | string / int | Hostname / IP cache engine      | 127.0.0.1 | no        |
| CACHE_PORT     | int          | port cache engine backend       |           | no        |
| CACHE_PREFIX   | string       | karakter awal tambahan pada key |           | no        |
| CACHE_TTL      | int          | waktu hidup / valid cache       | 10        | no        |

Contoh Provider Redis:

```sh
CACHE_PROVIDER=redis
CACHE_HOST=localhost
CACHE_PORT=1992
CACHE_PREFIX=terpusat_
CACHE_TTL=10
```

Contoh Provider Memcached:

```sh
CACHE_PROVIDER=memcached
CACHE_HOST=localhost
CACHE_PORT=12345
CACHE_PREFIX=terpusat_
CACHE_TTL=10
```

Atau menggunakan parameter yang dikirim langsung saat melakukan pemanggilan library.

```php
require_once '../shared/cache/index.php';

$cacheEngine = new BCache('redis-slave',  1992, 'redis', 'terpusat_', 0);
```

Sebelum dapat digunakan, pastikan beberapa extension yang diperlukan harus terinstall terlebih dahulu. Redis dan memcached memiliki dependency yang berbeda, silahkan baca selengkapnya di bawah ini:

#### memcached

Untuk menggunakan provider memcached, sebelumnya harus melakukan instalasi memcached extension. Untuk detail cara instalasi memcached silahkan baca beberapa referensi di bawah ini:

- [Memcached Book](https://www.php.net/manual/en/book.memcached.php)
- [memcached OSX](https://blog.bandhosting.nl/blog/install-memcached-on-php-7-4-on-osx)
- [memcached Ubuntu](https://serverpilot.io/docs/how-to-install-the-php-memcache-extension/)

#### Redis

Untuk menggunakan provider redis, sebelumnya harus melakukan instalasi redis extension. Untuk detail cara instalasi redis silahkan baca beberapa referensi di bawah ini:

- [Redis Book](https://github.com/phpredis/phpredis)
- [Redis Installation](https://github.com/phpredis/phpredis/blob/develop/INSTALL.markdown)

## Referensi

- [Memcache and Memcached](https://stackoverflow.com/questions/4937844/memcached-installed-in-theory-php-unable-to-use-memcache-connect)
- [Memcached IDE Intel](https://stackoverflow.com/questions/59367309/vscode-php-intelephense-show-error-when-using-memcached)
