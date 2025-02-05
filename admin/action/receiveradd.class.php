<?php

namespace zanithar\modules\news\admin\action;

use zanithar\modules\core\EXECUTION_TYPE;

class ReceiverAdd extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        $catid = $this->param()->getInt("catid");

        // Save new news
        if ($this->param()->postIf("news_save"))
        {
            $email = $this->param()->post("news_email");

            $statement = $this->prepare("
                INSERT INTO 
                    zanithar_PREFIX_news_receivers
                    ( 
                        `email`,
                        `category`
                    ) 
                    VALUES 
                    ( 
                        :email,
                        :category
                    )");

            $statement->bindParam(":email", $email);
            $statement->bindParam(":category", $catid);
            $statement->execute();

            $this->module()->core()->redirectAction("news", "receivershow", ["catid" => $catid]);
        }

        $this->assign("EMAIL", "");
        $this->assign("CATID", $catid);

        $this->render("receiveradd_edit");
    }
}
