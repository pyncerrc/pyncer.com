import hljs from 'highlight.js/lib/core';
import php from 'highlight.js/lib/languages/php';
import bash from 'highlight.js/lib/languages/bash';
import http from 'highlight.js/lib/languages/http';
hljs.registerLanguage('php', php);
hljs.registerLanguage('bash', bash);
hljs.registerLanguage('http', http);

import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    connect() {
        hljs.highlightElement(this.element)
    }
}
