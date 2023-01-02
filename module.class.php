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
        $this->registerAction("edit", EXECUTION_TYPE::ADMIN, false);
        $this->registerAction("enable", EXECUTION_TYPE::ADMIN, false);
        $this->registerAction("disable", EXECUTION_TYPE::ADMIN, false);
        $this->registerAction("delete", EXECUTION_TYPE::ADMIN, false);

        $this->registerAction("index", EXECUTION_TYPE::PUBLIC);
        $this->registerAction("detail", EXECUTION_TYPE::PUBLIC);
    }

    public function startup(): void
    {
    }

}
