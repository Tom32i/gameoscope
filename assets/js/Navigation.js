export default class Navigation {
    constructor() {
        this.screenshots = document.getElementsByClassName('screenshot');
        this.previousGameUrl = document.getElementById('game-previous').getAttribute('href');
        this.nextGameUrl = document.getElementById('game-next').getAttribute('href');
        this.index = 0;
        this.min = 0;
        this.max = this.screenshots.length - 1;

        this.onKey = this.onKey.bind(this);

        document.addEventListener('keyup', this.onKey);

        this.parse(window.location.hash);
    }

    onKey(event) {
        switch (event.keyCode) {
            // Left
            case 37:
                this.previous();
                break;

            // Right
            case 39:
                this.next();
                break;
        }
    }

    parse(hash) {
        switch(hash) {
            case '#screenshot-first':
                this.setIndex(0);
                break;

            case '#screenshot-last':
                this.setIndex(this.max);
                break;
        }
    }

    previous() {
        if (this.index === this.min) {
            return this.previousGame();
        }

        this.setIndex(this.index - 1);
    }

    next() {
        if (this.index === this.max) {
            return this.nextGame();
        }

        this.setIndex(this.index + 1);
    }

    setIndex(index) {
        this.index = index;

        window.location.hash = `#screenshot-${this.index}`;
    }

    previousGame() {
        window.location.href = `${this.previousGameUrl}#screenshot-last`;
    }

    nextGame() {
        window.location.href = `${this.nextGameUrl}#screenshot-first`;
    }
}
