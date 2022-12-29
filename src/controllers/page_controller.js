import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static outlets = [
        'nav',
        'theme',
        'locale',
    ]

    toggleOff(e) {
        this.navOutlets.forEach(nav => {
            if (!nav.element.contains(e.target)) {
                nav.toggleOff()
            }
        })

        this.themeOutlets.forEach(theme => {
            if (!theme.element.contains(e.target)) {
                theme.toggleOff()
            }
        })

        this.localeOutlets.forEach(locale => {
            if (!locale.element.contains(e.target)) {
                locale.toggleOff()
            }
        })
    }
}
