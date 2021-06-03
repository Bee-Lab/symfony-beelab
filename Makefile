EXEC = docker-compose exec

%:
	@:

args = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`

.PHONY: help start stop console dbupdate load test coverage update asset lint cs stan stylelint eslint npm deploy

help:
	@awk 'BEGIN {FS = ":.*##"; printf "Use: make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

start:	## start docker
	docker-compose up -d

stop: ## stop docker
	docker-compose stop

console: ## execute console with possible parameters
	${EXEC} php console $(call args,)

dbupdate: ## update database
	${EXEC} php console do:da:cr -n --if-not-exists
	${EXEC} phpunit console do:da:cr -n --if-not-exists
	${EXEC} php console do:c:clear-m
	${EXEC} php console do:s:u --force
	${EXEC} phpunit console do:s:u --force

load: ## load fixtures
	${EXEC} phpunit console do:fi:lo -n

test: ## execute tests
	${EXEC} phpunit phpunit --stop-on-failure

coverage: ## execute tests with coverage
	${EXEC} phpunit phpdbg -qrr bin/phpunit --coverage-html var/build

update: ## update vendors
	${EXEC} php composer update

asset: ## compile assets
	${EXEC} php node_modules/.bin/encore dev --watch

cs: ## execute fix coding standard (requires php-cs-fixer locally installed)
	${EXEC} -e COLUMNS=80 php php-cs-fixer fix -v

stan: ## execute static analysis (requires phpstan locally installed)
	${EXEC} php phpstan --memory-limit=-1 analyse

stylelint: ## execute style linting
	${EXEC} php npm run stylelint

eslint: ## execute js linting
	${EXEC} php npm run eslint

lint: ## make all linting (cs, stan, stylelint, eslint)
	- make cs
	- make stan
	- make stylelint
	- make eslint

npm: ## install frontend dependencies
	${EXEC} php npm install

deploy: ## deploy
	${EXEC} php dep deploy production
