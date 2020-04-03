.SILENT:
.PHONY: build

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

## Help
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

###########
# Install #
###########

## Install application
install:
	# Composer
	bin/composer install --verbose
	# Npm install
	npm install

install@staging: export APP_ENV = prod
install@staging:
	# Composer
	composer install --verbose --no-progress --no-interaction --prefer-dist --optimize-autoloader --no-scripts
	# Npm
	npm install

install@production: export APP_ENV = prod
install@production:
	# Composer
	composer install --verbose --no-progress --no-interaction --prefer-dist --optimize-autoloader --no-scripts --no-dev
	# Npm
	npm install

#######
# Run #
#######

## Run application
run:
	symfony server:start

#########
# Build #
#########

## Build application
watch:
	./node_modules/.bin/encore dev --watch

build:
	./node_modules/.bin/encore production

thumbnail:
	bin/console thumbnail:generate

thumbnail@prod: export SYMFONY_ENV = prod
thumbnail@prod:
	bin/console thumbnail:generate

clear-thumbnail:
	bin/console thumbnail:clear

clear-thumbnail@prod: export SYMFONY_ENV = prod
clear-thumbnail@prod:
	bin/console thumbnail:clear

############
# Security #
############

## Run security checks
security:
	security-checker security:check

security@test: export SYMFONY_ENV = test
security@test: security

########
# Lint #
########

lint: lint-phpcsfixer lint-phpstan lint-twig lint-yaml

fix-phpcsfixer:
	vendor/bin/php-cs-fixer fix

lint-phpcsfixer:
	vendor/bin/php-cs-fixer fix --dry-run --diff

lint-phpstan:
	vendor/bin/phpstan analyse

lint-twig:
	bin/console lint:twig templates

lint-yaml:
	bin/console lint:yaml translations config

##########
# Upload #
##########

## Upload photos (demo)
upload@demo:
	chmod -R 755 var/photos
	rsync -arzv --progress var/photos/* tom32i@deployer.vm:/home/tom32i/family-photos/shared/var/photos #--delete
	vendor/bin/dep thumbnail:generate deployer.vm

## Upload photos (prod)
upload@prod:
	chmod -R 755 var/photos
	rsync -arzv --progress var/photos/* tom32i@tom32i.fr:/home/tom32i/family-photos/shared/var/photos #--delete
	vendor/bin/dep thumbnail:generate tom32i.fr

## Download photos (demo)
download@demo:
	rsync -arzv --progress tom32i@deployer.vm:/home/tom32i/family-photos/shared/var/photos/* var/photos
	vendor/bin/dep thumbnail:generate deployer.vm

## Download photos (prod)
download@prod:
	rsync -arzv --progress tom32i@tom32i.fr:/home/tom32i/family-photos/shared/var/photos/* var/photos
	vendor/bin/dep thumbnail:generate tom32i.fr

##########
# Custom #
##########
