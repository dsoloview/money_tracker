prepare:
	sail php artisan key:generate
	sail php artisan migrate
	sail php artisan storage:link
