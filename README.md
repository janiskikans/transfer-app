# Transfer App

## Local setup

### Pre-requisites
* [Docker](https://www.docker.com/)

### Step-by-step setup

1. Clone this repository

```bash
git clone https://github.com/janiskikans/transfer-app.git
```

2. Navigate to the project root

```bash
cd transfer-app
```

3. Build docker images

```bash
make build
```
OR
```bash
docker compose build --pull --no-cache
```

4. Start containers

```bash
make up
```
OR
```bash
docker compose up
```
At this point the app database will be empty. So we need to populate it with test data.

5. Connect to the app container via bash or sh

```bash
make bash
```
OR
```bash
make sh
```

6. Run data seeders

```bash
./bin/console doctrine:fixtures:load
```

And enter `yes`. The database should now be filled with test data.

Let's now import currency rates from [exchangerate.host](https://exchangerate.host/).
7. Create a free account on [exchangerate.host](https://exchangerate.host/) and copy the API key to `.env.local` file in the project root directory (create one if it does not exist).

```txt
EXCHANGE_RATE_API_KEY=here-goes-your-api-key
```

8. Now import the currency rates using the following command from the app container

```bash
./bin/console currency:import-rates
```

9. You are ready to go! Check the API docs [here](http://localhost/api/doc) and go fire some requests! You can do that from the API documentation page itself or utilizing the `.http` requests under the `./http` directory via _PhpStorm_'s built-in HTTP client.

## API

### API documentation

You can find all API endpoints described in detail [here](http://localhost/api/doc).

⚠️ **Remember to start the app to view the documentation**.

### HTTP request collection

You can also find requests prepared in `.http` files under `/http` directory. You can fire these requests using _PhpStorm_.

⚠️ **Choose _development_ environment before firing the requests**.

#### ℹ️ Additional `/api/v1/client/all` endpoint

For demo purposes there is an additional `/api/v1/client/all` endpoint that lists all clients in the database.

## Running tests

### Preparing test database

Initialize the test DB database by running this from the app container

```bash
./bin/console --env=test doctrine:database:create
```

And init the schema with

```bash
./bin/console --env=test doctrine:schema:create
```

### Run test suites

#### Unit
```bash
php bin/phpunit --testsuite unit
```

#### Integration
```bash
php bin/phpunit --testsuite integration
```

### Run test suites with coverage

```bash
XDEBUG_MODE=coverage php bin/phpunit
```
