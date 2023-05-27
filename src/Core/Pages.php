<?php

namespace Appkit\Core;

use Appkit\Database\Db;
use PDO;

class Pages
{
    public static array $templates = [];

    public function __construct()
    {
        $this->setTemplates();
    }

    public function drafts(): ?array
    {
        $pdo = Db::connection()->getPdo();
        $query = $pdo->prepare("SELECT * FROM pages WHERE status IS NULL OR status = ''");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_CLASS, 'Appkit\Core\Page');
        if (count($result) === 0) {
            return null;
        }
        return $result;
    }

    public function find($slug): ?Page
    {
        $pdo = Db::connection()->getPdo();
        $stmt = $pdo->prepare('SELECT * FROM pages WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, 'Appkit\Core\Page');
        if (count($result) === 0) {
            return null;
        }
        return array_shift($result);
    }

    public function first(): ?Page
    {
        $pdo = Db::connection()->getPdo();
        $stmt = $pdo->prepare('SELECT * FROM pages LIMIT 1');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, 'Appkit\Core\Page');
        if (count($result) === 0) {
            return null;
        }
        return array_shift($result);
    }


    public function all(): array
    {
        $pdo = Db::connection()->getPdo();
        $query = $pdo->prepare('SELECT * FROM pages');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'Appkit\Core\Page');
    }

    private function setTemplates(): void
    {
        $templates = glob(Roots::TEMPLATES . DS . '*.php');
        foreach ($templates as $template) {
            $template = basename($template, '.php');
            static::$templates[] = $template;
        }
    }
}
