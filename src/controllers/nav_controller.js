import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['toggleLabel', 'toggleCheckbox', 'hamburger']

    initialize () {
        this._position = 0
    }

    toggle(event) {
        if (this.toggleCheckboxTarget.checked) {
            this.toggleOn(event)
        } else {
            this.toggleOff(event)
        }
    }

    toggleOn(event) {
        this.toggleCheckboxTarget.checked = true
        this.toggleLabelTarget.setAttribute('aria-expanded', 'true')
        this.hamburgerTarget.classList.add('hamburger_expanded')
        this._position = document.documentElement.scrollTop

        if (event && event.params && event.params.scroll) {
            window.scrollTo(0, 0)
        }
    }

    toggleOff(event) {
        this.toggleCheckboxTarget.checked = false
        this.toggleLabelTarget.setAttribute('aria-expanded', 'false')
        this.hamburgerTarget.classList.remove('hamburger_expanded')

        if (event && event.params && event.params.scroll) {
            window.scrollTo(0, this._position)
        }
    }
}
