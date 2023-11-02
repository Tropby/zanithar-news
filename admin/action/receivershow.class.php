<?php

namespace apexx\modules\news\admin\action;

class ReceiverShow extends \apexx\modules\core\IAction
{
    public function execute(): void
    {
        $catid = $this->param()->getInt("catid");

        $statement = $this->prepare(
            "SELECT 
                id AS ID, 
                `email` AS EMAIL
            FROM 
                APEXX_PREFIX_news_receivers
            WHERE 
                category = :category
            ORDER BY `email` ASC");
        $statement->bindParam(":category", $catid);
        $statement->execute();
        
        $this->assign("RECEIVERS", $statement->fetchAll());
        $this->assign("CATID", $catid);
        $this->render("receivershow");
    }
}
