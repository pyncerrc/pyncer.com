<?php
namespace Pyncer\Docs\Component\Theme;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Pyncer\Component\AbstractComponent;
use Pyncer\Component\Page\PageComponentInterface;
use Pyncer\Docs\Component\Theme\ThemeComponentInterface;
use Pyncer\Docs\Identifier as ID;
use Pyncer\Http\Message\HtmlResponse;
use Pyncer\Http\Message\JsonResponse;
use Pyncer\Http\Message\Status;

use function Pyncer\he as pyncer_he;
use function Pyncer\Http\url_equals as pyncer_http_url_equals;
use function Pyncer\Http\relative_url as pyncer_http_relative_url;

use const Pyncer\Docs\GITHUB_URL as DOCS_GITHUB_URL;
use const Pyncer\Docs\PROJECT_VERSION as DOCS_PROJECT_VERSION;
use const Pyncer\Docs\CSS_VERSION as DOCS_CSS_VERSION;
use const Pyncer\Docs\JS_VERSION as DOCS_JS_VERSION;

class PyncerTheme extends AbstractComponent implements
    ThemeComponentInterface
{
    private PageComponentInterface $page;

    public function setPageComponent(PageComponentInterface $page)
    {
        $this->page = $page;
    }

    final protected function getPrimaryResponse(): PsrResponseInterface
    {
        return new HtmlResponse(
            Status::SUCCESS_200_OK,
            $this->makeTemplate(),
        );
    }

    private function getPageData(array $page): array
    {
        foreach ($page as $key => $value) {
            if ($value instanceof JsonResponse) {
                $value = $value->getParsedBody();
                if (is_array($value)) {
                    $page[$key] = $this->getPageData($value);
                } else {
                    $page[$key] = $value;
                }
            } elseif ($value instanceof PsrResponseInterface) {
                $value = strval($value->getBody());
                if ($value === '') {
                    $page[$key] = null;
                } else {
                    $page[$key] = $value;
                }
            }
        }

        return $page;
    }

    private function makeTemplate(): string
    {
        $i18n = $this->get(ID::I18N);

        $page = $this->page->getResponse($this->handler);
        $page = $page->getParsedBody();
        $page = $this->getPageData($page);

        ob_start();
?>
<!DOCTYPE html>
<html lang="<?= pyncer_he($i18n->getShortCode()) ?>">
<head>
    <title><?= pyncer_he($page['head']['title']) ?></title>
    <meta name="description" content="<?= pyncer_he($page['head']['description']) ?>">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="msapplication-TileColor" content="#fafaf9">
    <meta name="msapplication-config" content="/favicon/browserconfig.xml">
    <meta name="theme-color" content="#fafaf9">
    <link href="/main.css?v=<?=DOCS_CSS_VERSION?>" rel="stylesheet">
    <script>
        try {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
                document.querySelector('meta[name="theme-color"]').setAttribute('content', '#000000')
            } else {
                document.documentElement.classList.remove('dark')
            }
        } catch (_) {}
    </script>
    <script src="/main.js?v=<?=DOCS_JS_VERSION?>"></script>
    <?= $this->makeHead($page) ?>
</head>
<body class="bg-stone-50 dark:bg-stone-900 text-stone-900 dark:text-stone-50">
<a class="sr-only" href="#main"><?= $i18n->get('text.skip-to-content') ?></a>
<div class="flex flex-col min-h-full justify-center lg:h-full lg:grid lg:grid-cols-[14rem_1fr] lg:grid-rows-[auto_1fr]"
    data-controller="page"
    data-action="click->page#toggleOff"
    data-page-theme-outlet=".toggle"
    data-page-locale-outlet=".toggle"
    data-page-nav-outlet=".toggle"
>
    <?= $this->makeHeader($page) ?>
    <?= $this->makeNav($page) ?>
    <?= $this->makeMain($page) ?>
    <?= $this->makeFooter($page) ?>
</div>
</body>
</html>
<?php
        return ob_get_clean();
    }

    private function makeHeader(array $page): string
    {
        $i18n = $this->get(ID::I18N);
        $router = $this->get(ID::ROUTER);

        $icon = null;
        $version = null;
        if (pyncer_http_url_equals(
            $router->getCurrentUrl(),
            $router->getIndexUrl()
        )) {
            ob_start();
?>
    <svg class="w-5 h-5 lg:w-8 lg:h-8 mr-1" viewBox="0 0 50 50" fill="currentColor" aria-hidden="true">
        <path d="M44.5,10.9C44.5,10.9,28,8,24,8c-5,0-7.1,2.6-8.8,6.6C14,17.3,10.1,25.3,8,29c-1.1,2-5.3,7.2-6,8l12,8c0,0,0.7-0.7,0.9-0.9	c3.2-3,7.2-6.7,9.8-8l0.5-0.2c1.8-0.6,3.5-1.4,4.7-2.6c0.2-0.3,0.7-0.9,0.8-1C32.9,29.2,37.1,27,41,27c1.9,0,3-0.2,3.6-0.6	c0,0,0.4-0.836,0.4-1.9c0-2.2-3.5-3.5-6-3.5h-1c-1.9,0-4.7,0.1-8.9,2.6c-0.4,0.3-0.9,0.4-1.3,0.4c-0.4,0-0.8-0.1-1.1-0.3	C25.2,22.9,24,20.3,24,18c0-1.2,1-3,4.7-3c0.9,0,2,0.1,3.3,0.3c0,0,11.8,1.7,12,1.7c0.6,0,1.2-0.2,1.7-0.6c0.6-0.4,1-1.2,1.2-2	C47.2,12.8,46.1,11.2,44.5,10.9z"/>
    </svg>
<?php
            $icon = ob_get_clean();

            $version = DOCS_PROJECT_VERSION;
        }

        $localeCodes = $i18n->getLocaleCodes();

        ob_start();
?>
    <header class="
            flex sticky top-0 z-30 items-center border-b px-2 py-1
            bg-stone-50 border-stone-300 dark:bg-stone-900 dark:border-stone-700
            lg:border-b-2 lg-py-0 lg:h-[3.25rem] lg:px-4 lg:col-span-2
        "
    >
        <h1 class="grow text-xl lg:text-3xl text-red-600 flex items-center">
            <?= $icon ?>

            <?= pyncer_he($page['header']['title']) ?>

            <?php if ($version !== null && $version !== '') { ?>
            <div class="
                    text-xs ml-2 border radius rounded p-1
                    text-red-600 border-red-600
                    lg:text-base lg:border-2 lg:py-0.5
                "
            >
                <?= pyncer_he($version) ?>
            </div>
            <?php } ?>
        </h1>

        <div class="flex pr-7 lg:pr-0">
            <?php if ($localeCodes) { ?>
            <div class="toggle relative" data-controller="locale">
                <button class="text-red-600 hover:text-red-400 transition-all duration-500 block w-5 h-5 lg:w-8 lg:h-8"
                    type="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-label="<?= pyncer_he($i18n->get('language.label')) ?>"
                    aria-controls="locale_list"
                    data-action="click->locale#toggle"
                    data-locale-target="toggleButton"
                >
                    <svg class="w-5 h-5 lg:w-8 lg:h-8" viewBox="0 0 50 50" fill="currentColor">
                        <path d="M 6 3 C 4.300781 3 3 4.300781 3 6 L 3 26 C 3 27.699219 4.300781 29 6 29 L 6.40625 29 L 8 27 L 6 27 C 5.398438 27 5 26.601563 5 26 L 5 6 C 5 5.398438 5.398438 5 6 5 L 26 5 C 26.601563 5 27 5.398438 27 6 L 27 21 L 24 21 C 22.300781 21 21 22.300781 21 24 L 21 27 L 16 27 L 17.59375 29 L 21 29 L 21 32.40625 L 23 34.09375 L 23 24 C 23 23.398438 23.398438 23 24 23 L 44 23 C 44.601563 23 45 23.398438 45 24 L 45 44 C 45 44.601563 44.601563 45 44 45 L 24 45 C 23.398438 45 23 44.601563 23 44 L 23 42 L 21 43.6875 L 21 44 C 21 45.699219 22.300781 47 24 47 L 44 47 C 45.699219 47 47 45.699219 47 44 L 47 24 C 47 22.300781 45.699219 21 44 21 L 29 21 L 29 6 C 29 4.300781 27.699219 3 26 3 Z M 16 8 L 16 10 L 8 10 L 8 12 L 19.90625 12 C 19.597656 14.226563 18.292969 16.054688 16.65625 17.53125 C 14.148438 15.332031 12.875 13.03125 12.875 13.03125 L 11.125 13.96875 C 11.125 13.96875 12.433594 16.378906 15.0625 18.78125 C 14.996094 18.828125 14.941406 18.890625 14.875 18.9375 C 12.234375 20.757813 9.59375 21.65625 9.59375 21.65625 L 10.21875 23.5625 C 10.21875 23.5625 13.125 22.597656 16.03125 20.59375 C 16.238281 20.449219 16.449219 20.28125 16.65625 20.125 C 17.796875 20.96875 19.125 21.742188 20.625 22.34375 L 21.375 20.46875 C 20.226563 20.011719 19.199219 19.417969 18.28125 18.78125 C 20.109375 17.050781 21.636719 14.792969 21.9375 12 L 25 12 L 25 10 L 18 10 L 18 8 Z M 12 25 L 7 31 L 10 31 L 10 35 L 14 35 L 14 31 L 17 31 Z M 33 26.40625 L 27.8125 40.1875 L 30.3125 40.1875 L 31.40625 37 L 36.6875 37 L 37.8125 40.1875 L 40.3125 40.1875 L 35.09375 26.40625 Z M 34 29.40625 L 36 35.09375 L 32 35.09375 Z M 19 33 L 19 36 L 10 36 L 14 40 L 19 40 L 19 43 L 25 38 Z"/>
                    </svg>
                </button>

                <ul class="
                        absolute hidden top-[2.0625rem] right-[-0.75rem] min-w-[6rem] border py-1 rounded
                        bg-stone-50 dark:bg-stone-900 border-stone-300 dark:border-stone-700
                        xs:right-1/2 translate-x-1/2
                        lg:top-[3rem] lg:border-2
                    "
                    id="theme_list"
                    data-locale-target="list"
                >
                    <?php
                        foreach ($localeCodes as $key => $localeCode) {
                            $url = $router->getCurrentLocaleUrl(
                                $localeCode,
                            );
                            $url = pyncer_http_relative_url($url);

                            $class = '';

                            if ($key > 0) {
                                $class .= ' mt-1';
                            }

                            if ($i18n->getCode() === $localeCode) {
                                $class .= ' bg-stone-200 dark:bg-stone-800';
                            }
                    ?>
                    <li class="flex items-center cursor-pointer<?= $class ?>">
                        <a class="block grow hover:bg-stone-200 whitespace-nowrap dark:hover:bg-stone-800 px-2"
                            href="<?= pyncer_he($url) ?>"
                            data-action="keydown.esc->locale#toggleOff"
                        >
                            <?= pyncer_he($i18n->getLocale($localeCode)->getName()) ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>

            <div class="toggle relative ml-2 lg:ml-4" data-controller="theme"
                data-theme-selected-class="bg-stone-200 dark:bg-stone-800"
            >
                <button class="text-red-600 hover:text-red-400 transition-all duration-500 block w-5 h-5 lg:w-8 lg:h-8"
                    type="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-label="<?= pyncer_he($i18n->get('theme.label')) ?>"
                    aria-controls="theme_list"
                    data-action="click->theme#toggle"
                    data-theme-target="toggleButton"
                >
                    <span class="dark:hidden">
                        <svg class="w-5 h-5 lg:w-8 lg:h-8" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M 11.875 0.1875 C 11.371094 0.25 10.996094 0.679688 11 1.1875 L 11 3.1875 C 10.996094 3.546875 11.183594 3.882813 11.496094 4.066406 C 11.808594 4.246094 12.191406 4.246094 12.503906 4.066406 C 12.816406 3.882813 13.003906 3.546875 13 3.1875 L 13 1.1875 C 13.003906 0.898438 12.878906 0.625 12.664063 0.433594 C 12.449219 0.242188 12.160156 0.152344 11.875 0.1875 Z M 4 3.375 C 3.625 3.441406 3.324219 3.714844 3.21875 4.078125 C 3.113281 4.445313 3.222656 4.835938 3.5 5.09375 L 4.90625 6.5 C 5.148438 6.796875 5.535156 6.933594 5.910156 6.847656 C 6.28125 6.761719 6.574219 6.46875 6.660156 6.097656 C 6.746094 5.722656 6.609375 5.335938 6.3125 5.09375 L 4.90625 3.6875 C 4.71875 3.488281 4.460938 3.378906 4.1875 3.375 C 4.15625 3.375 4.125 3.375 4.09375 3.375 C 4.0625 3.375 4.03125 3.375 4 3.375 Z M 19.6875 3.375 C 19.460938 3.40625 19.25 3.519531 19.09375 3.6875 L 17.6875 5.09375 C 17.390625 5.335938 17.253906 5.722656 17.339844 6.097656 C 17.425781 6.46875 17.71875 6.761719 18.089844 6.847656 C 18.464844 6.933594 18.851563 6.796875 19.09375 6.5 L 20.5 5.09375 C 20.796875 4.808594 20.886719 4.367188 20.726563 3.988281 C 20.570313 3.609375 20.191406 3.367188 19.78125 3.375 C 19.75 3.375 19.71875 3.375 19.6875 3.375 Z M 12 5.1875 C 8.15625 5.1875 5 8.34375 5 12.1875 C 5 16.03125 8.15625 19.1875 12 19.1875 C 15.84375 19.1875 19 16.03125 19 12.1875 C 19 8.34375 15.84375 5.1875 12 5.1875 Z M 12 7.1875 C 14.753906 7.1875 17 9.433594 17 12.1875 C 17 14.941406 14.753906 17.1875 12 17.1875 C 9.246094 17.1875 7 14.941406 7 12.1875 C 7 9.433594 9.246094 7.1875 12 7.1875 Z M 0.8125 11.1875 C 0.261719 11.238281 -0.144531 11.730469 -0.09375 12.28125 C -0.0429688 12.832031 0.449219 13.238281 1 13.1875 L 3 13.1875 C 3.359375 13.191406 3.695313 13.003906 3.878906 12.691406 C 4.058594 12.378906 4.058594 11.996094 3.878906 11.683594 C 3.695313 11.371094 3.359375 11.183594 3 11.1875 L 1 11.1875 C 0.96875 11.1875 0.9375 11.1875 0.90625 11.1875 C 0.875 11.1875 0.84375 11.1875 0.8125 11.1875 Z M 20.8125 11.1875 C 20.261719 11.238281 19.855469 11.730469 19.90625 12.28125 C 19.957031 12.832031 20.449219 13.238281 21 13.1875 L 23 13.1875 C 23.359375 13.191406 23.695313 13.003906 23.878906 12.691406 C 24.058594 12.378906 24.058594 11.996094 23.878906 11.683594 C 23.695313 11.371094 23.359375 11.183594 23 11.1875 L 21 11.1875 C 20.96875 11.1875 20.9375 11.1875 20.90625 11.1875 C 20.875 11.1875 20.84375 11.1875 20.8125 11.1875 Z M 5.46875 17.59375 C 5.25 17.632813 5.054688 17.742188 4.90625 17.90625 L 3.5 19.28125 C 3.101563 19.667969 3.097656 20.304688 3.484375 20.703125 C 3.871094 21.101563 4.507813 21.105469 4.90625 20.71875 L 6.3125 19.3125 C 6.636719 19.011719 6.722656 18.535156 6.527344 18.140625 C 6.335938 17.742188 5.902344 17.523438 5.46875 17.59375 Z M 18.1875 17.59375 C 17.8125 17.660156 17.511719 17.933594 17.40625 18.296875 C 17.300781 18.664063 17.410156 19.054688 17.6875 19.3125 L 19.09375 20.71875 C 19.492188 21.105469 20.128906 21.101563 20.515625 20.703125 C 20.902344 20.304688 20.898438 19.667969 20.5 19.28125 L 19.09375 17.90625 C 18.886719 17.683594 18.585938 17.570313 18.28125 17.59375 C 18.25 17.59375 18.21875 17.59375 18.1875 17.59375 Z M 11.875 20.1875 C 11.371094 20.25 10.996094 20.679688 11 21.1875 L 11 23.1875 C 10.996094 23.546875 11.183594 23.882813 11.496094 24.066406 C 11.808594 24.246094 12.191406 24.246094 12.503906 24.066406 C 12.816406 23.882813 13.003906 23.546875 13 23.1875 L 13 21.1875 C 13.003906 20.898438 12.878906 20.625 12.664063 20.433594 C 12.449219 20.242188 12.160156 20.152344 11.875 20.1875 Z"/>
                        </svg>
                    </span>
                    <span class="hidden dark:inline">
                        <svg class="w-5 h-5 lg:w-8 lg:h-8" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M 8 0 L 7.1875 2.1875 L 5 3 L 7.1875 3.8125 L 8 6 L 8.8125 3.8125 L 11 3 L 8.8125 2.1875 Z M 3 6 L 2.1875 8.1875 L 0 9 L 2.1875 9.8125 L 3 12 L 3.8125 9.8125 L 6 9 L 3.8125 8.1875 Z M 15 6 C 10.054688 6 6 10.054688 6 15 C 6 19.945313 10.054688 24 15 24 C 19.707031 24 23.550781 20.40625 24 15.8125 L 24.125 14.34375 L 22.71875 14.75 C 22.078125 14.933594 21.605469 15 21 15 C 18.246094 15 16 12.753906 16 10 C 16 9.082031 16.257813 8.273438 16.6875 7.5 L 17.5 6 Z M 14.5 8.09375 C 14.3125 8.722656 14 9.304688 14 10 C 14 13.84375 17.15625 17 21 17 C 21.21875 17 21.417969 16.925781 21.625 16.90625 C 20.757813 19.832031 18.214844 22 15 22 C 11.144531 22 8 18.855469 8 15 C 8 11.320313 10.890625 8.363281 14.5 8.09375 Z"/>
                        </svg>
                    </span>
                </button>

                <ul class="
                        absolute hidden top-[2.0625rem] right-[1rem] min-w-[6rem]
                        bg-stone-50 dark:bg-stone-900 border border-stone-300
                        dark:border-stone-700 py-1 rounded
                        xs:right-1/2 translate-x-1/2
                        lg:top-[3rem] lg:border-2
                    "
                    id="theme_list"
                    data-theme-target="list"
                >
                    <li class="flex items-center cursor-pointer whitespace-nowrap hover:bg-stone-200 dark:hover:bg-stone-800 px-2"
                        tabindex="0"
                        data-theme-target="listItemLight"
                        data-action="click->theme#light keydown.enter->theme#light keydown.esc->theme#toggleOff"
                    >
                        <svg class="w-4 h-4 mr-2 fill-red-600" viewBox="0 0 24 24">
                            <path d="M 11.875 0.1875 C 11.371094 0.25 10.996094 0.679688 11 1.1875 L 11 3.1875 C 10.996094 3.546875 11.183594 3.882813 11.496094 4.066406 C 11.808594 4.246094 12.191406 4.246094 12.503906 4.066406 C 12.816406 3.882813 13.003906 3.546875 13 3.1875 L 13 1.1875 C 13.003906 0.898438 12.878906 0.625 12.664063 0.433594 C 12.449219 0.242188 12.160156 0.152344 11.875 0.1875 Z M 4 3.375 C 3.625 3.441406 3.324219 3.714844 3.21875 4.078125 C 3.113281 4.445313 3.222656 4.835938 3.5 5.09375 L 4.90625 6.5 C 5.148438 6.796875 5.535156 6.933594 5.910156 6.847656 C 6.28125 6.761719 6.574219 6.46875 6.660156 6.097656 C 6.746094 5.722656 6.609375 5.335938 6.3125 5.09375 L 4.90625 3.6875 C 4.71875 3.488281 4.460938 3.378906 4.1875 3.375 C 4.15625 3.375 4.125 3.375 4.09375 3.375 C 4.0625 3.375 4.03125 3.375 4 3.375 Z M 19.6875 3.375 C 19.460938 3.40625 19.25 3.519531 19.09375 3.6875 L 17.6875 5.09375 C 17.390625 5.335938 17.253906 5.722656 17.339844 6.097656 C 17.425781 6.46875 17.71875 6.761719 18.089844 6.847656 C 18.464844 6.933594 18.851563 6.796875 19.09375 6.5 L 20.5 5.09375 C 20.796875 4.808594 20.886719 4.367188 20.726563 3.988281 C 20.570313 3.609375 20.191406 3.367188 19.78125 3.375 C 19.75 3.375 19.71875 3.375 19.6875 3.375 Z M 12 5.1875 C 8.15625 5.1875 5 8.34375 5 12.1875 C 5 16.03125 8.15625 19.1875 12 19.1875 C 15.84375 19.1875 19 16.03125 19 12.1875 C 19 8.34375 15.84375 5.1875 12 5.1875 Z M 12 7.1875 C 14.753906 7.1875 17 9.433594 17 12.1875 C 17 14.941406 14.753906 17.1875 12 17.1875 C 9.246094 17.1875 7 14.941406 7 12.1875 C 7 9.433594 9.246094 7.1875 12 7.1875 Z M 0.8125 11.1875 C 0.261719 11.238281 -0.144531 11.730469 -0.09375 12.28125 C -0.0429688 12.832031 0.449219 13.238281 1 13.1875 L 3 13.1875 C 3.359375 13.191406 3.695313 13.003906 3.878906 12.691406 C 4.058594 12.378906 4.058594 11.996094 3.878906 11.683594 C 3.695313 11.371094 3.359375 11.183594 3 11.1875 L 1 11.1875 C 0.96875 11.1875 0.9375 11.1875 0.90625 11.1875 C 0.875 11.1875 0.84375 11.1875 0.8125 11.1875 Z M 20.8125 11.1875 C 20.261719 11.238281 19.855469 11.730469 19.90625 12.28125 C 19.957031 12.832031 20.449219 13.238281 21 13.1875 L 23 13.1875 C 23.359375 13.191406 23.695313 13.003906 23.878906 12.691406 C 24.058594 12.378906 24.058594 11.996094 23.878906 11.683594 C 23.695313 11.371094 23.359375 11.183594 23 11.1875 L 21 11.1875 C 20.96875 11.1875 20.9375 11.1875 20.90625 11.1875 C 20.875 11.1875 20.84375 11.1875 20.8125 11.1875 Z M 5.46875 17.59375 C 5.25 17.632813 5.054688 17.742188 4.90625 17.90625 L 3.5 19.28125 C 3.101563 19.667969 3.097656 20.304688 3.484375 20.703125 C 3.871094 21.101563 4.507813 21.105469 4.90625 20.71875 L 6.3125 19.3125 C 6.636719 19.011719 6.722656 18.535156 6.527344 18.140625 C 6.335938 17.742188 5.902344 17.523438 5.46875 17.59375 Z M 18.1875 17.59375 C 17.8125 17.660156 17.511719 17.933594 17.40625 18.296875 C 17.300781 18.664063 17.410156 19.054688 17.6875 19.3125 L 19.09375 20.71875 C 19.492188 21.105469 20.128906 21.101563 20.515625 20.703125 C 20.902344 20.304688 20.898438 19.667969 20.5 19.28125 L 19.09375 17.90625 C 18.886719 17.683594 18.585938 17.570313 18.28125 17.59375 C 18.25 17.59375 18.21875 17.59375 18.1875 17.59375 Z M 11.875 20.1875 C 11.371094 20.25 10.996094 20.679688 11 21.1875 L 11 23.1875 C 10.996094 23.546875 11.183594 23.882813 11.496094 24.066406 C 11.808594 24.246094 12.191406 24.246094 12.503906 24.066406 C 12.816406 23.882813 13.003906 23.546875 13 23.1875 L 13 21.1875 C 13.003906 20.898438 12.878906 20.625 12.664063 20.433594 C 12.449219 20.242188 12.160156 20.152344 11.875 20.1875 Z"/>
                        </svg>
                        <?= pyncer_he($i18n->get('light.label')) ?>
                    </li>
                    <li class="flex items-center cursor-pointer whitespace-nowrap hover:bg-stone-200 dark:hover:bg-stone-800 px-2 mt-1"
                        tabindex="0"
                        data-theme-target="listItemDark"
                        data-action="click->theme#dark keydown.enter->theme#dark keydown.esc->theme#toggleOff"
                    >
                        <svg class="w-4 h-4 mr-2 fill-red-600" viewBox="0 0 24 24">
                            <path d="M 8 0 L 7.1875 2.1875 L 5 3 L 7.1875 3.8125 L 8 6 L 8.8125 3.8125 L 11 3 L 8.8125 2.1875 Z M 3 6 L 2.1875 8.1875 L 0 9 L 2.1875 9.8125 L 3 12 L 3.8125 9.8125 L 6 9 L 3.8125 8.1875 Z M 15 6 C 10.054688 6 6 10.054688 6 15 C 6 19.945313 10.054688 24 15 24 C 19.707031 24 23.550781 20.40625 24 15.8125 L 24.125 14.34375 L 22.71875 14.75 C 22.078125 14.933594 21.605469 15 21 15 C 18.246094 15 16 12.753906 16 10 C 16 9.082031 16.257813 8.273438 16.6875 7.5 L 17.5 6 Z M 14.5 8.09375 C 14.3125 8.722656 14 9.304688 14 10 C 14 13.84375 17.15625 17 21 17 C 21.21875 17 21.417969 16.925781 21.625 16.90625 C 20.757813 19.832031 18.214844 22 15 22 C 11.144531 22 8 18.855469 8 15 C 8 11.320313 10.890625 8.363281 14.5 8.09375 Z"/>
                        </svg>
                        <?= pyncer_he($i18n->get('dark.label')) ?>
                    </li>
                    <li class="flex items-center cursor-pointer whitespace-nowrap hover:bg-stone-200 dark:hover:bg-stone-800 px-2 mt-1"
                        tabindex="0"
                        data-theme-target="listItemSystem"
                        data-action="click->theme#system keydown.enter->theme#system keydown.esc->theme#toggleOff"
                    >
                        <svg class="w-4 h-4 mr-2 fill-red-600" viewBox="0 0 24 24">
                            <path d="M 0 4 L 0 19 L 8 19 L 8 21 L 16 21 L 16 19 L 24 19 L 24 4 L 0 4 z M 2 6 L 22 6 L 22 17 L 2 17 L 2 6 z"/>
                        </svg>
                        <?= pyncer_he($i18n->get('system.label')) ?>
                    </li>
                </ul>
            </div>

            <?php if (DOCS_GITHUB_URL !== '') { ?>
            <a class="hidden xs:block text-red-600 hover:text-red-400 transition-all duration-500 w-5 h-5 lg:w-8 lg:h-8 ml-2 lg:ml-4"
                href="<?= pyncer_he(DOCS_GITHUB_URL) ?>"
                target="_blank"
            >
                <span class="sr-only">
                    <?= pyncer_he($i18n->get('github.label')) ?>
                </span>
                <svg class="w-5 h-5 lg:w-8 lg:h-8 fill-current" viewBox="0 0 16 16" aria-hidden="true">
                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/>
                </svg>
            </a>
        </div>
        <?php } ?>
    </header>
<?php
        return ob_get_clean();

    }
    private function makeNav(array $page): string
    {
        $i18n = $this->get(ID::I18N);

        ob_start();
?>
    <nav class="toggle lg:relative lg:w-56 lg:overflow-y-auto" role="navigation" data-controller="nav">
        <input
            class="hidden"
            type="checkbox"
            id="menu_toggle"
            data-nav-target="toggleCheckbox"
            data-action="change->nav#toggle"
            data-nav-scroll-param="true"
        >

        <label
            class="block fixed right-2 top-2 w-5 h-5 lg:w-6 lg:h-6 lg:hidden z-50"
            for="menu_toggle"
            aria-expanded="false"
            aria-controls="nav"
            data-nav-target="toggleLabel"
        >
            <span class="hamburger relative block w-5 h-5 lg:w-6 lg:h-6 cursor-pointer" aria-hidden="true" data-nav-target="hamburger">
                <span class="transition-all duration-500 block absolute w-full h-1"></span>
                <span class="transition-all duration-500 block absolute top-1 left-0 w-full h-1 mt-1 lg:mt-1.5"></span>
                <span class="transition-all duration-500 block absolute w-full h-1 mt-1 lg:mt-1.5"></span>
            </span>
            <span class="sr-only"><?= pyncer_he($i18n->get('text.nav-toggle')) ?></span>
        </label>

        <ul class="
                flex flex-col text-base bg-stone-50 dark:bg-stone-900 border-b border-stone-300 dark:border-stone-700 absolute w-full p-2
                lg:static lg:w-56 lg:text-lg lg:border-b-0 lg:p-4
            "
            id="nav"
        >
<?php
        foreach ($page['nav'] as $item) {
            $class = '';

            if ($item['selected']) {
                $class = ' bg-stone-200 dark:bg-stone-800';
            }

            $icon = '';
            if ($item['icon'] ?? null) {
                ob_start();

                if ($item['icon'] === 'back') {
?>
    <svg class="w-4 h-4 fill-red-600 mr-1" viewBox="0 0 32 32" aria-hidden="true">
        <path d="M 10.8125 9.28125 L 4.09375 16 L 10.8125 22.71875 L 12.21875 21.28125 L 7.9375 17 L 28 17 L 28 15 L 7.9375 15 L 12.21875 10.71875 Z"/>
    </svg>
<?php
                }

                $icon = ob_get_clean();
            }

            $url = pyncer_http_relative_url($item['url']);
?>
            <li class="mt-1 first:mt-0">
                <a class="hover:bg-stone-200 dark:hover:bg-stone-800 rounded-md px-2 flex items-center<?= $class ?>" href="<?= pyncer_he($url) ?>">
                    <?= $icon ?>
                    <?= pyncer_he($item['title']) ?>
                </a>
            </li>
<?php
        }
?>
        </ul>
    </nav>
<?php
        return ob_get_clean();
    }
    private function makeBreadcrumb(array $page): string
    {
        if ($page['breadcrumb'] === null) {
            return '';
        }

        $i18n = $this->get(ID::I18N);
        $ariaLabel = $i18n->get('breadcrumb.label');

        ob_start();
?>
        <nav class="mb-2" aria-label="<?= pyncer_he($ariaLabel) ?>">
            <ol class="text-xs lg:text-sm flex flex-wrap pb-2">
<?php
        foreach ($page['breadcrumb'] as $item) {
            $url = pyncer_http_relative_url($item['url']);
?>
                <li class="after:content-['/'] after:px-1 flex">
                    <a class="text-red-600 hover:underline underline-offset-8" href="<?= pyncer_he($url) ?>">
                        <?= pyncer_he($item['title']) ?>
                    </a>
                </li>
<?php
        }
?>
            </ol>
        </nav>
<?php
        return ob_get_clean();

    }
    private function makeMain(array $page): string
    {
        $router = $this->get(ID::ROUTER);

        $main = str_replace(
            [
                '<h2>',
                '<p>',
                '<a ',
                ' href="http',
                '<code ',
                '<code>',
                '<pre>',
            ],
            [
                '<h2 class="text-xl lg:text-3xl mb-4 mt-6 first:mt-0 font-semibold">',
                '<p class="text-sm lg:text-lg mb-4 max-w-xl">',
                '<a class="text-red-600 underline underline-offset-4 lg:underline-offset-8 hover:no-underline" ',
                ' target="_blank" href="http',
                '<code data-controller="code" ',
                '<code class="hljs">',
                '<pre class="text-xs lg:text-sm mb-4">',
            ],
            $page['main'] ?? ''
        );

        // Localize all relative urls
        $pattern = '/href="(\/[^"]+)"/';
        preg_match_all($pattern, $main, $matches);

        // Loop through the matches and apply the callback function to each URL
        foreach ($matches[1] as $url) {
            $parts = explode('?', $url);

            $localizedUrl = $router->getUrl($parts[0], $parts[1] ?? []);
            $localizedUrl = pyncer_http_relative_url($localizedUrl);

            $main = str_replace(
                'href="' . $url . '"',
                'href="' . $localizedUrl . '"',
                $main
            );
        }

        ob_start();
?>
    <main class="grow p-2 lg:pl-8 lg:pr-4 lg:py-4 overflow-y-auto lg:border-stone-300 dark:lg:border-stone-700" id="main">
        <?= $this->makeI18nNotice() ?>
        <?= $this->makeBreadcrumb($page) ?>
        <div>
        <?= $main ?>
        </div>
    </main>
<?php
        return ob_get_clean();
    }

    private function makeI18nNotice(): string
    {
        $i18n = $this->get(ID::I18N);
        $router = $this->get(ID::ROUTER);

        if (!pyncer_http_url_equals(
            $router->getCurrentUrl(),
            $router->getIndexUrl()
        )) {
            return '';
        }

        if ($i18n->getShortCOde() === 'en') {
            return '';
        }

        ob_start();
?>
        <div class="border lg:border-2 rounded border-red-600 flex items-center mb-2 max-w-xl">
            <svg class="shrink-0 fill-red-600 w-8 h-8 mr-1" viewBox="0 0 128 128">
                <path d="M 64 23.599609 C 60.3 23.599609 57.099219 25.499609 55.199219 28.599609 L 19.099609 88.900391 C 17.199609 92.100391 17.1 95.999219 19 99.199219 C 20.8 102.39922 24.100781 104.40039 27.800781 104.40039 L 100.09961 104.40039 C 103.79961 104.40039 107.2 102.49922 109 99.199219 C 110.8 95.999219 110.80039 92.100391 108.90039 88.900391 L 72.800781 28.599609 C 70.900781 25.499609 67.7 23.599609 64 23.599609 z M 64 29.599609 C 65.5 29.599609 66.799609 30.299219 67.599609 31.699219 L 103.80078 92 C 104.60078 93.3 104.60039 94.900781 103.90039 96.300781 C 103.10039 97.600781 101.79922 98.400391 100.19922 98.400391 L 27.800781 98.400391 C 26.300781 98.400391 24.899609 97.600781 24.099609 96.300781 C 23.299609 95.000781 23.399219 93.3 24.199219 92 L 60.400391 31.699219 C 61.200391 30.399219 62.5 29.599609 64 29.599609 z M 64 49.300781 C 62.3 49.300781 61 50.600781 61 52.300781 L 61 73.300781 C 61 75.000781 62.3 76.300781 64 76.300781 C 65.7 76.300781 67 75.000781 67 73.300781 L 67 52.300781 C 67 50.600781 65.7 49.300781 64 49.300781 z M 64 80.5 A 3 3 0 0 0 61 83.5 A 3 3 0 0 0 64 86.5 A 3 3 0 0 0 67 83.5 A 3 3 0 0 0 64 80.5 z"/>
            </svg>
            <?= $i18n->get('notice.i18n') ?>
        </div>
<?php
        return ob_get_clean();
    }

    private function makeFooter(array $page): string
    {
        // TODO Related links and things?
        return '';
    }

    private function makeHead(array $page): string
    {
        // TODO JSON-LD, OG, Twitter, etc
        return '';
    }
}
