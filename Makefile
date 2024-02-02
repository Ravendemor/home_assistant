SHELL = /bin/sh

install-project:
	docker compose up -d
	docker compose exec laravel composer install
	docker compose exec laravel php artisan key:generate
	docker compose exec laravel php artisan migrate
