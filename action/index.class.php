<?php

namespace apexx\modules\news\action;

use apexx\modules\core\EXECUTION_TYPE;

class Index extends \apexx\modules\core\IAction
{
    public function execute(): void
    {
        /*** @var \apexx\modules\user\Module */
        $userModule = $this->module()->core()->module("user");
        $user = $userModule->currentUser();

        $statement = $this->prepare("
            SELECT 
                id AS ID, 
                UNIX_TIMESTAMP(`time`) AS `TIME`, 
                title as TITLE, 
                `text` AS `TEXT`
            FROM
                APEXX_PREFIX_news      
            
                " . ($user->hasRight("news", "index", EXECUTION_TYPE::ADMIN) ? "" : " WHERE UNIX_TIMESTAMP(`time`) < ".time()." AND `time` IS NOT NULL ") . "
            ORDER BY 
                `time` DESC
            ");

        $statement->execute();
        $content = $statement->fetchAll();

        if ( $content )
        {
            $this->assign("CURRENT_TIME", time());
            $this->assign("NEWS", $content);
            $this->render("index");
        }
        else
        {
            throw new \Exception("Can not show news. News unavailable.");
        }
    }
}