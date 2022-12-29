The [pyncer/component](https://github.com/pyncerrc/pyncer-component) package
handles generating the body of an HTPP response.

## Installation

Install via [Composer](https://getcomposer.org):

```bash
$ composer require pyncer/component
```

## Elements

Elements are for generating portions of content that make up pages and modules.

## Pages

Pages represent the content of a Web APP's page and can be made up of multiple
elements. While they are meant for returning HTML, they can also return
headless content such as when making an AJAX request.

## Modules

Modules are used for providing headless content such as the JSON returned by a
REST API request.

## Component Decorators

Component decorators are used to surround a page or module with additional
content. They can be used for making themes or providing content that is
generally found on all pages or modules.
