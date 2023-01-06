<?php
namespace Pyncer\Docs\Component\Page;

use Pyncer\Component\Page\AbstractPage;
use Pyncer\Http\Message\Factory\StreamFactory;
use Pyncer\Http\Message\Response;
use Pyncer\Http\Message\Status;
use Pyncer\Docs\Component\Forge\HasSubPagesTrait;
use Pyncer\Docs\Identifier as ID;

use function date;
use function dirname;
use function filemtime;

use const DIRECTORY_SEPARATOR as DS;
use const Pyncer\ENCODING as PYNCER_ENCODING;

class SitemapPage extends AbstractPage
{
    use HasSubPagesTrait;

    protected function getResponseData(): mixed
    {
        $router = $this->get(ID::ROUTER);

        $dir = dirname($this->dir);
        $url = $router->getIndexUrl();

        $time = filemtime($dir . DS . 'index.md');
        $date = date('Y-m-d', $time);

        ob_start();
?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>http://pyncer.com/</loc>
        <?php if ($date !== false) { ?>
        <lastmod><?= $date ?></lastmod>
        <?php } ?>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>
    <?= $this->makeMarkdownPageUrls($dir, $url, 0.9) ?>
</urlset>
<?php
        $xml = ob_get_clean();

        $stream = (new StreamFactory())->createStream($xml);

        $encoding = strtolower(PYNCER_ENCODING);

        return new Response(
            headers: [
                'Content-Type' => 'application/xml; charset=' . $encoding,
            ],
            body: $stream,
        );
    }

    protected function makeMarkdownPageUrls(
        string $dir,
        string $url,
        float $priority,
    ): string
    {
        $pages = $this->getPages($dir);

        $xml = [];

        foreach ($pages as $page) {
            if (file_exists($dir . DS . $page . '.md')) {
                $time = filemtime($dir . DS . $page . '.md');
            } else {
                $time = filemtime($dir . DS . $page . DS . 'index.md');
            }

            $date = date('Y-m-d', $time);
            ob_start();
?>
    <url>
        <loc><?= $url . '/' . $page ?></loc>
        <?php if ($date !== false) { ?>
        <lastmod><?= $date ?></lastmod>
        <?php } ?>
        <changefreq>weekly</changefreq>
        <priority><?= $priority ?></priority>
    </url>
<?php

            $xml[] = ob_get_clean();

            $xml[] = $this->makeMarkdownPageUrls(
                $dir . DS . $page,
                $url . '/' . $page,
                $priority - 0.1,
            );
        }

        return implode("\n", $xml);
    }
}
