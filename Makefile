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

## Setup project
setup: install var/games

## Install application
install:
	# Composer
	composer install --verbose
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

# Create screenshot repository
var/games:
	cd var && git clone git@tom32i.fr:/home/git/gameoscope-screenshot.git games

#######
# Run #
#######

## Run application
start:
	symfony server:start --no-tls

## Serve build
serve:
	php -S 0.0.0.0:8001 -t build

#########
# Build #
#########

## Watch asets
watch:
	npx encore dev --watch

## Build application
build: build-assets cache build-content optimize

build-assets:
	npx encore production

build-content: export APP_ENV = prod
build-content:
	symfony console cache:clear
	bin/console stenope:build

build@staging: build
build@production: build

optimize:
	npx optimage-cli --config="optimage.json"

cache: export APP_ENV = prod
cache:
	bin/console showcase:cache-generate

clear-cache: export APP_ENV = prod
clear-cache:
	bin/console showcase:cache-clear

############
# Security #
############

## Run security checks
security:
	symfony check:security

security@test: export APP_ENV = test
security@test: security

########
# Lint #
########

lint: lint-phpcsfixer lint-phpstan lint-twig lint-yaml lint-eslint lint-stylelint

lint-phpcsfixer: export PHP_CS_FIXER_IGNORE_ENV = true
lint-phpcsfixer:
	vendor/bin/php-cs-fixer fix

lint-phpstan:
	vendor/bin/phpstan analyse src

lint-twig:
	bin/console lint:twig templates

lint-yaml:
	bin/console lint:yaml translations config

lint-eslint:
	npx eslint assets/js --ext .js,.json --fix

lint-stylelint:
	npx stylelint 'assets/css/**/*.scss' --fix

##########
# Deploy #
##########

## Build and deploy to staging
deploy@staging: build
	rsync -arzv --delete build/* tom32i@deployer.vm:/home/tom32i/gameoscope/

## Build and deploy to production
deploy@production: build
	rsync -arzv --delete build/* tom32i@tom32i.fr:/home/tom32i/gameoscope/

##########
# Custom #
##########

## Create a new game directory
game:
	bin/console app:game

cache-generate:
	bin/console showcase:cache-generate full
	curl localhost:8000

cache-clear:
	bin/console showcase:cache-clear

cache-regenerate: cache-clear cache-generate

normalize:
	bin/console showcase:normalize-names
