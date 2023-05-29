<?php

namespace Appkit\Core;

use Appkit\Http\Response;
use Appkit\Http\Stream;
use Appkit\Layouts\Layout;
use Appkit\Layouts\Template;
use Appkit\Toolkit\Dir;
use Appkit\Toolkit\Obj;
use Appkit\Toolkit\Tpl;
use Appkit\Toolkit\V;

class Page extends Obj
{
    public string $template = 'default';
    public ?string $dir = null;
    public array $files = [];

    public function __construct($data = [])
    {
        $content = V::json($this->content) ? json_decode($this->content, true) : [];

        parent::__construct(array_merge($content, $data));

        parent::__construct();
        $this->setTemplate();
        $this->setFiles();
    }

    public function dir(): string
    {
        return $this->dir;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function setFiles()
    {
        $path = BASE_DIR . '/content/pages/' . $this->slug;
        if (Dir::exists($path) === false) {
            return $this;
        }
        $this->files = Dir::read($path);
        return $this;
    }

    public function template(): string
    {
        return $this->template;
    }

    public function setTemplate(): Page
    {
        if (!in_array($this->template, Pages::$templates)) {
            $this->template = 'default';
        }
        return $this;
    }

    public function render(): Response
    {
        $body = (new Template($this->template))->render(['page' => $this]);
        return (new Response(200, ['Content-Type' => 'text/html'], Stream::create($body)));
    }
}
