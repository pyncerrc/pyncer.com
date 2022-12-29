<?php
namespace Pyncer\Docs\Component\Element;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\Component\Element\AbstractElement;
use Pyncer\Docs\Component\Forge\HasSubPagesTrait;
use Pyncer\Docs\Identifier as ID;

use function Pyncer\IO\clean_dir as pyncer_io_clean_dir;

use const DIRECTORY_SEPARATOR as DS;

class BreadcrumbElement extends AbstractElement
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

        $items = [];

        if (!$this->paths) {
            return $items;
        }

        $items[] = [
            'title' => $i18n->get('page.title'),
            'url' => $router->getIndexUrl()
        ];

        $currentPath = '';

        foreach ($this->paths as $path) {
            $currentPath .= '/' . $path;

            $title = $i18n->get('page.' . ltrim($currentPath, '/') . '.title');

            $items[] = [
                'title' => $title,
                'url' => $router->getUrl($currentPath)
            ];
        }

        $dir = $this->dir . DS . implode(DS, $this->paths);
        if (!$this->hasSubPages($dir)) {
            array_pop($items);
        }

        return $items;
    }
}
