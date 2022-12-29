import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = [
        'toggleButton',
        'list',
    ]

    toggle() {
        const open = (this.toggleButtonTarget.getAttribute('aria-expanded') === 'true')

        if (open) {
            this.toggleOff();
        } else {
            this.toggleOn();
        }
    }

    toggleOn() {
        this.toggleButtonTarget.setAttribute('aria-expanded', 'true')
        this.listTarget.classList.remove('hidden')
    }

    toggleOff() {
        this.toggleButtonTarget.setAttribute('aria-expanded', 'false')
        this.listTarget.classList.add('hidden')
    }
}
