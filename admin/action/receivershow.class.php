<?php

namespace zanithar\modules\news\admin\action;

class ReceiverShow extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        $catid = $this->param()->getInt("catid");

        $statement = $this->prepare(
            "SELECT 
                id AS ID, 
                `email` AS EMAIL
            FROM 
                ZCMS_PREFIX_news_receivers
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
