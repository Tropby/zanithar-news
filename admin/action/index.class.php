<?php

namespace apexx\modules\news\admin\action;


class Index extends \apexx\modules\core\IAction
{
    public function execute(): void
    {
        $statement = $this->prepare("SELECT id AS ID, UNIX_TIMESTAMP(`time`) AS `TIME`, title as TITLE FROM APEXX_PREFIX_news");
        $statement->execute();
        $this->assign("PAGES", $statement->fetchAll());
        $this->assign("TIME", time());
        $this->render("index");
    }
}
