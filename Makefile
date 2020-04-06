.SILENT:
.PHONY: build

-include .manala/make/Makefile

###########
# Install #
###########

## Install application
install:
	# Composer
	composer install --verbose
	# Npm install
	npm install
	# Create screenshot repository
	make var/games

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

##########
# Warmup #
##########

warmup:
	# Generate thumbnails

# Note: This task is invoked after a deployment to staging
warmup@staging: export APP_ENV = prod
warmup@staging:
	# Generate thumbnails

# Note: This task is invoked after a deployment to production
warmup@production: export APP_ENV = prod
warmup@production:
	# Generate thumbnails


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

build@staging: build
build@production: build

thumbnail:
	bin/console thumbnail:generate

thumbnail@production: export SYMFONY_ENV = prod
thumbnail@production: thumbnail

clear-thumbnail:
	bin/console thumbnail:clear

clear-thumbnail@production: export SYMFONY_ENV = prod
clear-thumbnail@production: clear-thumbnail

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

## Upload photos (staging)
upload@staging:
	chmod -R 755 var/games
	rsync -arzv --progress --exclude '.*' var/games/* tom32i@deployer.vm:/home/tom32i/gameoscope/shared/var/games #--delete
	#vendor/bin/dep thumbnail:generate deployer.vm

## Upload photos (production)
upload@production:
	chmod -R 755 var/games
	rsync -arzv --progress --exclude '.*' var/games/* tom32i@tom32i.fr:/home/tom32i/gameoscope/shared/var/games #--delete
	#vendor/bin/dep thumbnail:generate tom32i.fr

## Download photos (staging)
download@staging:
	rsync -arzv --progress --exclude '.*' tom32i@deployer.vm:/home/tom32i/gameoscope/shared/var/games/* var/games
	#vendor/bin/dep thumbnail:generate deployer.vm

## Download photos (production)
download@production:
	rsync -arzv --progress --exclude '.*' tom32i@tom32i.fr:/home/tom32i/gameoscope/shared/var/games/* var/games
	#vendor/bin/dep thumbnail:generate tom32i.fr

##########
# Custom #
##########

game:
	bin/console app:game
