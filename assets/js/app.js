import Navigation from './Navigation';

function onLoad() {
    const screenshots = document.getElementsByClassName('screenshot');

    if (screenshots.length) {
        new Navigation(
            screenshots,
            document.getElementById('game-previous'),
            document.getElementById('game-next')
        );
    }
}

window.addEventListener('load', onLoad);
