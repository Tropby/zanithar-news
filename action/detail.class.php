<?php

namespace apexx\modules\news\action;

use apexx\modules\core\EXECUTION_TYPE;

class Detail extends \apexx\modules\core\IAction
{
    public function execute(): void
    {
        $id = null;
        if ($this->param()->getIf("id"))
            $id = $this->param()->getInt("id");

        if( !$id )
        {            
            if($this->param()->getIf("name"))
            {
                $name = $this->param()->getUrlDecoded("name");
                $statement = $this->prepare("
                    SELECT 
                        id
                    FROM
                        APEXX_PREFIX_news
                    WHERE
                        `title` LIKE :name 
                    LIMIT 1");
                $statement->bindParam(":name", $name);
                $statement->execute();
                $id = $statement->fetch();
                if( $id )
                    $id = $id["id"];
            }
            else
            {
                throw new \Exception("Can not find content!");
            }
        }

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
                a.id = :id ".( $user->hasRight("news", "index", EXECUTION_TYPE::ADMIN) ? "" : " AND a.time < NOW() " ) );
        $statement->bindParam(":id", $id);
        $statement->execute();
        $content = $statement->fetch();

        if( $content )
        {
            $this->assign("TIME", $content["TIME"]);
            $this->assign("CURRENT_TIME",time());
            $this->assign("TITLE", $content["TITLE"]);
            $this->assign("CAT_NAME", $content["CAT_NAME"]);
            $this->assign("ID", $content["ID"]);
            $this->assign("TEXT", $content["TEXT"]);
            $this->render("detail");
        }
        else
        {
            throw new \Exception("Can not show news. News unavailable.");
        }
    }
}
