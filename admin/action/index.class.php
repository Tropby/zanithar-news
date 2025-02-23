<?php

namespace zanithar\modules\news\admin\action;


class Index extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        $statement = $this->prepare(
            "SELECT 
                a.id AS ID, 
                UNIX_TIMESTAMP(`time`) AS `TIME`, 
                a.title as TITLE,
                b.name AS CAT_NAME
            FROM 
                ZCMS_PREFIX_news AS a
            LEFT JOIN 
                ZCMS_PREFIX_news_category AS b ON (a.category = b.id)
            ORDER BY `time` DESC");
        $statement->execute();
        $this->assign("PAGES", $statement->fetchAll());
        $this->assign("TIME", time());
        $this->render("index");
    }
}
