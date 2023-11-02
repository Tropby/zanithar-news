<?php

namespace apexx\modules\news;

use apexx\modules\core\EXECUTION_TYPE;

class Module extends \apexx\modules\core\IModule
{
    public function __construct(\apexx\modules\core\Core $core)
    {
        parent::__construct(
            $core,
            "news",
            "2.0.0"
        );

        // Register all template functions
        $this->registerAction("index", EXECUTION_TYPE::ADMIN);
        $this->registerAction("add", EXECUTION_TYPE::ADMIN);
        $this->registerAction("catshow", EXECUTION_TYPE::ADMIN);
        $this->registerAction("edit", EXECUTION_TYPE::ADMIN, false);
        $this->registerAction("enable", EXECUTION_TYPE::ADMIN, false);
        $this->registerAction("disable", EXECUTION_TYPE::ADMIN, false);
        $this->registerAction("delete", EXECUTION_TYPE::ADMIN, false);

        $this->registerAction("catadd", EXECUTION_TYPE::ADMIN, false);
        $this->registerAction("catedit", EXECUTION_TYPE::ADMIN, false);
        $this->registerAction("catdelete", EXECUTION_TYPE::ADMIN, false);

        $this->registerAction("receivershow", EXECUTION_TYPE::ADMIN, false);
        $this->registerAction("receiveradd", EXECUTION_TYPE::ADMIN, false);
        $this->registerAction("receiverdelete", EXECUTION_TYPE::ADMIN, false);

        $this->registerAction("index", EXECUTION_TYPE::PUBLIC);
        $this->registerAction("detail", EXECUTION_TYPE::PUBLIC);
    }

    public function startup(): void
    {
        $this->core()->callFunction("registerNaviItem", [ "News", "list", "news.html"]);

        $stmt = $this->core()->db()->prepare("SELECT * FROM APEXX_PREFIX_news_category ORDER BY `name` ASC");
        $stmt->execute();
        $cats = $stmt->fetchAll();
        foreach($cats as $cat)
        {
            $this->core()->callFunction("registerNaviItem", [ "News Category [".$cat["name"]."]", "list", "news/category/".$cat["id"]."/".$cat["name"]]);
        }
    }

    public function htaccess(): array
    {
        return [            
            "RewriteRule ^news$ index.php?module=news&action=index [L]",
            "RewriteRule ^news.html$ index.php?module=news&action=index [L]",
            "RewriteRule ^news/(\d+)/(.+) index.php?module=news&action=detail&id=$1&name=$2 [B,BNP]",
            "RewriteRule ^news/category/(\d+)/(.+)$ index.php?module=news&action=index&catid=$1&name=$2 [B,BNP]"
        ];
    }
}
