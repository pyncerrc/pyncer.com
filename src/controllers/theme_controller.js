import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = [
        'toggleButton',
        'list',
        'listItemLight',
        'listItemDark',
        'listItemSystem',
    ]

    static classes = [
        'selected'
    ]

    connect() {
        if (localStorage.theme === 'dark') {
            this.listItemDarkTarget.setAttribute('aria-selected', 'true')
            this.listItemDarkTarget.classList.add(...this.selectedClasses)
        } else if (localStorage.theme === 'light') {
            this.listItemLightTarget.setAttribute('aria-selected', 'true')
            this.listItemLightTarget.classList.add(...this.selectedClasses)
        } else {
            this.listItemSystemTarget.setAttribute('aria-selected', 'true')
            this.listItemSystemTarget.classList.add(...this.selectedClasses)
        }
    }

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

    dark() {
        localStorage.setItem('theme', 'dark')

        this._reset()

        this.listItemDarkTarget.setAttribute('aria-selected', 'true')
        this.listItemDarkTarget.classList.add(...this.selectedClasses)

        document.documentElement.classList.add('dark')
        document.querySelector('meta[name="theme-color"]').setAttribute('content', '#1c1917')
    }

    light() {
        localStorage.setItem('theme', 'light')

        this._reset()

        this.listItemLightTarget.setAttribute('aria-selected', 'true')
        this.listItemLightTarget.classList.add(...this.selectedClasses)

        document.documentElement.classList.remove('dark')
        document.querySelector('meta[name="theme-color"]').setAttribute('content', '#fafaf9')
    }

    system() {
        localStorage.removeItem('theme')

        this._reset()

        this.listItemSystemTarget.setAttribute('aria-selected', 'true')
        this.listItemSystemTarget.classList.add(...this.selectedClasses)

        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark')
            document.querySelector('meta[name="theme-color"]').setAttribute('content', '#1c1917')
        } else {
            document.documentElement.classList.remove('dark')
            document.querySelector('meta[name="theme-color"]').setAttribute('content', '#fafaf9')
        }
    }

    _reset() {
        this.listItemDarkTarget.setAttribute('aria-selected', 'false')
        this.listItemLightTarget.setAttribute('aria-selected', 'false')
        this.listItemSystemTarget.setAttribute('aria-selected', 'false')

        this.listItemDarkTarget.classList.remove(...this.selectedClasses)
        this.listItemLightTarget.classList.remove(...this.selectedClasses)
        this.listItemSystemTarget.classList.remove(...this.selectedClasses)

        this.toggleButtonTarget.setAttribute('aria-expanded', 'false')
        this.listTarget.classList.add('hidden')
    }
}
