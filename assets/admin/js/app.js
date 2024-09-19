import SpoilerSwitch from './SpoilerSwitch.js';

function onLoad() {
    document.querySelectorAll('.spoil-switch').forEach(element => new SpoilerSwitch(element));
}

window.addEventListener('load', onLoad);
