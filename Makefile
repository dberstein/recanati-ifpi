.PHONY: build
build:
	@docker build -t ifpi .

.PHONY: composer
composer:
	@composer update --dev && composer du --dev

.PHONY: run
run: build
	@docker run --rm -p 8080:80 --name ifpi ifpi

.PHONY: sync
sync:
	@rsync -avz -e 'ssh -i ~/.ssh/id_rsa_new' $(PWD)/ basegeo.com:/home/daniel/nginx-proxy/ifpi/

.PHONY: unsync
unsync:
	@rsync -avz -e 'ssh -i ~/.ssh/id_rsa_new' basegeo.com:/home/daniel/nginx-proxy/ifpi/ $(PWD)/

