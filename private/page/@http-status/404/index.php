<?php
use Pyncer\Docs\Component\Page\HttpStatusPage;
use Pyncer\Http\Message\Status;

$page = new HttpStatusPage($request, __DIR__, $paths);
$page->setStatus(Status::CLIENT_ERROR_404_NOT_FOUND);

return $page;
