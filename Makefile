EXEC = docker compose exec

%:
	@:

args = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`

.PHONY: build help start stop console bash dbupdate load test coverage update asset lint cs twigcs stan stylelint eslint npm deploy

help:
	@awk 'BEGIN {FS = ":.*##"; printf "Use: make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

build:	## build docker
	docker compose build

start:	## start docker
	docker compose up -d

stop: ## stop docker
	docker compose stop

console: ## execute console with possible parameters
	${EXEC} php bin/console $(call args,) --profile -v

bash: ## enter the container shell
	${EXEC} php bash

dbupdate: ## update the database
	${EXEC} php bin/console do:da:cr -n --if-not-exists
	${EXEC} -e APP_ENV=test php bin/console do:da:cr -n --if-not-exists
	${EXEC} php bin/console do:c:clear-m
	${EXEC} php bin/console do:s:u --force
	${EXEC} -e APP_ENV=test php bin/console do:s:u --force

load: ## load the fixtures
	${EXEC} -e APP_ENV=test php bin/console do:fi:lo -n

test: ## execute the tests
	${EXEC} -e XDEBUG_MODE=off php bin/phpunit --stop-on-failure --stop-on-error --display-warnings

coverage: ## execute the tests with coverage
	${EXEC} -e XDEBUG_MODE=coverage php bin/phpunit --coverage-html var/build

update: ## update vendors
	${EXEC} php composer update

asset: ## compile the assets
	${EXEC} php npm run watch

cs: ## execute coding standard fix (requires php-cs-fixer locally installed)
	${EXEC} -e XDEBUG_MODE=off php bin/php-cs-fixer fix -v

twigcs: ## execute twig coding standard fix
	${EXEC} -e XDEBUG_MODE=off php bin/twig-cs-fixer lint templates --fix

stan: ## execute static analysis (requires phpstan locally installed)
	${EXEC} -e XDEBUG_MODE=off php bin/phpstan analyse -v

stylelint: ## execute style linting
	${EXEC} php npm run stylelint

eslint: ## execute js linting
	${EXEC} php npm run eslint

lint: ## lint all the things!
	- make cs
	- make stan
	- make stylelint
	- make eslint
	- make console lint:twig templates
	- make "console lint:yaml config --parse-tags"
	- make twigcs

npm: ## install the frontend dependencies
	${EXEC} php npm install

npm-update: ## update the frontend dependencies
	${EXEC} php npm update --include=dev

deploy: ## deploy to production
	${EXEC} php bin/dep deploy production
