<?php

namespace apexx\modules\news\admin\action;

use apexx\modules\core\EXECUTION_TYPE;

class CatAdd extends \apexx\modules\core\IAction
{
    public function execute(): void
    {
        // Save new news
        if ($this->param()->postIf("news_save"))
        {
            $name = $this->param()->post("news_name");

            $statement = $this->prepare("
                INSERT INTO 
                    APEXX_PREFIX_news_category
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
