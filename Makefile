run: build up init

build:
	@echo "[INFO] Creating new docker network "
	docker network create skinny 2> /dev/null; true
	mkdir -p _volumes
	docker compose build --no-cache

init:
	docker compose exec app composer install
	docker compose exec app php artisan migrate:fresh
	docker compose exec app php artisan migrate:fresh --env=testing

queue:
	docker compose exec app php artisan queue:work

up:
	docker compose up -d

down:
	docker compose down -v --remove-orphans

stop:
	docker compose stop

bash:
	docker compose exec app bash

ps:
	docker compose ps

tests:
	docker compose exec app ./vendor/bin/phpunit

cc:
	docker compose exec app bash -c 'php artisan route:clear && php artisan config:clear && php artisan cache:clear && php artisan event:clear'

cl:
	docker compose exec app bash -c 'truncate --size 0 /var/www/app/storage/logs/*.log && echo \"Finish to clear all *.log\"'
