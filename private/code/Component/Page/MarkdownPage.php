<?php
namespace Pyncer\Docs\Component\Page;

use Pyncer\Component\Page\AbstractPage;
use Pyncer\Docs\Component\Element\BreadcrumbElement;
use Pyncer\Docs\Component\Element\FooterElement;
use Pyncer\Docs\Component\Element\HeadElement;
use Pyncer\Docs\Component\Element\HeaderElement;
use Pyncer\Docs\Component\Element\MainElement;
use Pyncer\Docs\Component\Element\NavElement;
use Pyncer\Docs\Identifier as ID;

use const DIRECTORY_SEPARATOR as DS;

class MarkdownPage extends AbstractPage
{
    protected function getResponseData(): mixed
    {
        $head = new HeadElement(
            $this->request,
            $this->dir,
            $this->paths,
        );

        $header = new HeaderElement(
            $this->request,
            $this->dir,
            $this->paths,
        );

        $nav = new NavElement(
            $this->request,
            $this->dir,
            $this->paths,
        );

        $breadcrumb = new BreadcrumbElement(
            $this->request,
            $this->dir,
            $this->paths,
        );

        $main = new MainElement(
            $this->request,
            $this->dir,
            $this->paths,
        );

        $footer = new FooterElement(
            $this->request,
            $this->dir,
            $this->paths,
        );

        return [
            'head' => $head,
            'header' => $header,
            'nav' => $nav,
            'breadcrumb' => $breadcrumb,
            'main' => $main,
            'footer' => $footer,
        ];
    }

    protected function isValidPath(): bool
    {
        if (!$this->paths) {
            return true;
        }

        $file = $this->dir . DS . implode(DS, $this->paths) . DS .'index.md';

        if (file_exists($file)) {
            return true;
        }

        $file = $this->dir . DS . implode(DS, $this->paths) . '.md';

        if (file_exists($file)) {
            return true;
        }

        return false;
    }
}
