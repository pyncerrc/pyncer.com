<?php
namespace Pyncer\Docs\Component\Page;

use Pyncer\Component\Page\AbstractPage;
use Pyncer\Http\Message\Status;
use Pyncer\Docs\Component\Element\BreadcrumbElement;
use Pyncer\Docs\Component\Element\FooterElement;
use Pyncer\Docs\Component\Element\HeadElement;
use Pyncer\Docs\Component\Element\HeaderElement;
use Pyncer\Docs\Component\Element\MainElement;
use Pyncer\Docs\Component\Element\NavElement;
use Pyncer\Docs\Identifier as ID;

class HttpStatusPage extends AbstractPage
{
    private Status $status = Status::CLIENT_ERROR_404_NOT_FOUND;

    public function getStatus(): Status
    {
        return $this->status;
    }
    public function setStatus(Status $value): static
    {
        $this->status = $value;
        return $this;
    }

    protected function getResponseData(): mixed
    {
        $i18n = $this->get(ID::I18N);

        $code = $this->getStatus()->getStatusCode();

        /* $title = $i18n->get(
            key: 'syntax.join_title',
            args: [
                $i18n->get('page.http-status-' . $code . '.title'),
                $i18n->get('page.title')
            ]
        ); */

        $dir = dirname(dirname($this->dir));

        $head = new HeadElement(
            $this->request,
            $dir,
            ['http-status-' . $code],
        );

        $header = new HeaderElement(
            $this->request,
            $dir,
            ['http-status-' . $code],
        );

        $nav = new NavElement(
            $this->request,
            $dir,
            [],
        );

        $breadcrumb = null;

        $main = new MainElement(
            $this->request,
            $this->dir,
            [],
        );

        $footer = new FooterElement(
            $this->request,
            $dir,
            [],
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
        return true;
    }
}
