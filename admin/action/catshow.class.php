<?php

namespace apexx\modules\news\admin\action;

class CatShow extends \apexx\modules\core\IAction
{
    public function execute(): void
    {
        $statement = $this->prepare(
            "SELECT 
                id AS ID, 
                `name` AS NAME
            FROM 
                APEXX_PREFIX_news_category 
            ORDER BY `name` ASC");
        $statement->execute();
        $this->assign("CATEGORIES", $statement->fetchAll());
        $this->render("catshow");
    }
}
