PHPSTAN_LEVEL := 7

.PHONY: build
build:
	@docker build -t ifpi .

run/dev: phpstan test build
	@docker run --rm -p 8080:80 --name ifpi -v $(PWD)/src/html:/var/www/html ifpi

.PHONY: run
run: build
	@docker run --rm -p 8080:80 --name ifpi ifpi

.PHONY: run/prod
run/prod: phpstan build
	@docker run -d -p 8080:80 --name ifpi --restart unless-stopped -v $(PWD)/src/html:/var/www/html ifpi

.PHONY: sync
sync:
	@rsync -avz -e 'ssh -i ~/.ssh/id_rsa_new' $(PWD)/ basegeo.com:/home/daniel/nginx-proxy/ifpi/

.PHONY: unsync
unsync:
	@rsync -avz -e 'ssh -i ~/.ssh/id_rsa_new' basegeo.com:/home/daniel/nginx-proxy/ifpi/ $(PWD)/

.PHONY: test
test:
	@vendor/bin/phpunit --stop-on-defect tests

.PHONY: composer
composer:
	@composer update --dev && composer du --dev

