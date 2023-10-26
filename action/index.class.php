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

        $statement = $this->prepare(
            "SELECT 
                a.id AS ID, 
                UNIX_TIMESTAMP(`time`) AS `TIME`, 
                a.title as TITLE, 
                `text` AS `TEXT`,
                b.name AS CAT_NAME
            FROM
                APEXX_PREFIX_news AS a   
            LEFT JOIN 
                APEXX_PREFIX_news_category AS b ON (a.category = b.id)
                
                WHERE
                " . ( $this->param()->getIf("catid") ? "b.id = " . $this->param()->getInt("catid")." AND " : "" ). "
                " . ($user->hasRight("news", "index", EXECUTION_TYPE::ADMIN) ? "" : "  UNIX_TIMESTAMP(`time`) < ".time()." AND `time` IS NOT NULL AND ") . "
                1=1

            ORDER BY 
                `time` DESC
            ");

        $statement->execute();
        $content = $statement->fetchAll();

        if ( $content )
        {
            $this->assign("CURRENT_TIME", time());
            $this->assign("NEWS", $content);

            if( $this->param()->getIf("catid") )
            {
                $catid = $this->param()->getInt("catid");
                $stmt = $this->prepare("SELECT `name` AS `NAME` FROM APEXX_PREFIX_news_category WHERE id = :id");
                $stmt->bindParam(":id", $catid);
                $stmt->execute();
                $this->assign("CAT_NAME", $stmt->fetch()["NAME"]);
            }
            else
            {
                $this->assign("CAT_NAME", NULL);
            }

            $this->render("index");
        }
        else
        {
            throw new \Exception("Can not show news. News unavailable.");
        }
    }
}