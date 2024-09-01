# Gameoscope

A handmade collection of beautiful screenshots from my favorite videogames.

## Setup

**Prerequisites**

    brew install pkg-config imagemagick
    pecl install imagick

Install dependencies and setup games folder:

    make setup

## Create a new game

    make game

Put the screenshots in `games/{my-game}`.

## Development

Start asset watcher:

    make watch

Start Symfony server:

    make start

Go to [http://localhost:8000](http://localhost:8000)

## Test the build

Build the static website:

    make build

Start the static server:

    make serve

Go to [http://localhost:8001](http://localhost:8001)

## Deploy

Deploy to production:

    make deploy@production
