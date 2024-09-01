export default class Spoiler {
    constructor(toggle, content, spoils) {
        this.toggle = toggle;
        this.content = content;
        this.spoils = spoils;
        this.className = 'show-spoilers';

        this.onClick = this.onClick.bind(this);

        this.toggle.addEventListener('click', this.onClick);

        spoils.forEach(spoil => spoil.addEventListener('click', this.onClick));
    }

    get active() {
        return this.content.classList.contains(this.className);
    }

    onClick(event) {
        if (this.active && event.currentTarget !== this.toggle) {
            return;
        }

        event.preventDefault();

        this.content.classList.toggle(this.className);
    }
}
