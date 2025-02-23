<?php

namespace zanithar\modules\news\admin\action;

use zanithar\modules\core\EXECUTION_TYPE;

class CatAdd extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        // Save new news
        if ($this->param()->postIf("news_save"))
        {
            $name = $this->param()->post("news_name");

            $statement = $this->prepare("
                INSERT INTO 
                    ZCMS_PREFIX_news_category
                    ( 
                        `name`
                    ) 
                    VALUES 
                    ( 
                        :name
                    )");

            $statement->bindParam(":name", $name);
            $statement->execute();

            $this->module()->core()->redirectAction("news", "catshow");
        }

        $this->assign("NAME", "");
        $this->assign("ID", "");

        $this->render("catadd_edit");
    }
}
