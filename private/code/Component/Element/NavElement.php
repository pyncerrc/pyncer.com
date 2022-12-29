<?php
namespace Pyncer\Docs\Component\Element;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\Component\Element\AbstractElement;
use Pyncer\Docs\Component\Forge\HasSubPagesTrait;
use Pyncer\Docs\Identifier as ID;

use function Pyncer\IO\clean_dir as pyncer_io_clean_dir;

use const DIRECTORY_SEPARATOR as DS;

class NavElement extends AbstractElement
{
    use HasSubPagesTrait;

    protected ?string $dir;
    protected array $paths;

    public function __construct(
        PsrServerRequestInterface $request,
        ?string $dir,
        array $paths,
    ) {
        parent::__construct($request);

        $this->dir = ($dir !== null ? pyncer_io_clean_dir($dir) : $dir);
        $this->paths = $paths;
    }

    protected function getResponseData(): mixed
    {
        $i18n = $this->get(ID::I18N);
        $router = $this->get(ID::ROUTER);

        $backTitle = null;
        $backUrl = null;

        if ($this->paths) {
            $dir = $this->dir . DS . implode(DS, $this->paths);

            // Display parent path if no sub pages
            if (!$this->hasSubPages($dir)) {
                $paths = $this->paths;
                array_pop($paths);

                if ($paths) {
                    $currentPath = '/' . implode('/', $paths);
                    $dir = $this->dir . DS . implode(DS, $paths);
                    $title = $i18n->get('page.' . ltrim($currentPath, '/') . '.title');
                    $url = $router->getUrl($currentPath);

                    // Back link
                    array_pop($paths);
                    if ($paths) {
                        $backCurrentPath = '/' . implode('/', $paths);
                        $backTitle = $i18n->get('page.' . ltrim($backCurrentPath, '/') . '.title');
                        $backUrl = $router->getUrl($backCurrentPath);
                    } else {
                        $backTitle = $i18n->get('page.title');
                        $backUrl = $router->getIndexUrl();
                    }
                } else {
                    $currentPath = '';
                    $dir = $this->dir;
                    $title = $i18n->get('page.title');
                    $url = $router->getIndexUrl();
                }
                $selected = false;
            } else {
                $currentPath = '/' . implode('/', $this->paths);
                $title = $i18n->get('page.' . ltrim($currentPath, '/') . '.title');
                $url = $router->getUrl($currentPath);
                $selected = true;

                // Back link
                $paths = $this->paths;
                array_pop($paths);
                if ($paths) {
                    $backCurrentPath = '/' . implode('/', $paths);
                    $backTitle = $i18n->get('page.' . ltrim($backCurrentPath, '/') . '.title');
                    $backUrl = $router->getUrl($backCurrentPath);
                } else {
                    $backTitle = $i18n->get('page.title');
                    $backUrl = $router->getIndexUrl();
                }
            }
        } else {
            $currentPath = '';
            $title = $i18n->get('page.title');
            $url = $router->getIndexUrl();
            $dir = $this->dir;
            $selected = true;
        }

        $title = $i18n->get('overview.title');

        $items = [];

        if ($backUrl) {
            $items[] = [
                'title' => $backTitle,
                'url' => $backUrl,
                'selected' => false,
                'icon' => 'back',
            ];
        }

        $items[] = [
            'title' => $title,
            'url' => $url,
            'selected' => $selected,
        ];

        $selectedHandled = $selected;

        $pages = $this->getPages($dir);

        foreach ($pages as $page) {
            $selected = false;
            if (!$selectedHandled &&
                $this->paths[count($this->paths) - 1] === $page
            ) {
                $selected = true;
            }

            $url = $router->getUrl($currentPath . '/' . $page);

            if ($currentPath === '') {
                $i18nKey = $page;
            } else {
                $i18nKey = $currentPath . '/' . $page;
            }

            $items[] = [
                'title' => $i18n->get('page.' . ltrim($i18nKey, '/') . '.title'),
                'url' => $url,
                'selected' => $selected,
            ];
        }

        return $items;
    }
}
