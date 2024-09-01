export default class SpoilerSwitch {
    constructor(element) {
        this.element = element;

        this.onChange = this.onChange.bind(this);

        this.element.addEventListener('change', this.onChange);
    }

    onChange(event) {
        window.location = this.element.dataset.action;
    }
}
