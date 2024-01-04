# BileMo

## Installing dependencies

```
composer install
```

## Launch the project locally

In a terminal, run:

```
symfony serve
```

## Configure the database connection

In `.env.local`, fill the DATABASE_URL variable depending on the database engine you want to use. In your terminal, type :

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

## Configure the JWT authentication

In your terminal, type :

```
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

You have to type three times the same pass phrase in the terminal.

In `.env.local`, fill the JWT_PASSPHRASE variable with the password you just entered.

