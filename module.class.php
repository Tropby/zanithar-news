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
        $this->registerAction("show", EXECUTION_TYPE::ADMIN);
    }

    public function startup(): void
    {
    }

}
