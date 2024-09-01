import Navigation from './Navigation';
import Spoiler from './Spoiler';

function onLoad() {
    const screenshots = document.getElementsByClassName('screenshot');

    if (screenshots.length) {
        new Navigation(
            screenshots,
            document.getElementById('game-previous'),
            document.getElementById('game-next')
        );

        new Spoiler(
            document.getElementById('toggle-spoil'),
            document.querySelector('main.content'),
            document.querySelectorAll('.spoil'),
        );
    }
}

window.addEventListener('load', onLoad);
