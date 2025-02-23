<?php

namespace zanithar\modules\news\admin\action;

use zanithar\modules\core\EXECUTION_TYPE;

class CatEdit extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        if( !$this->param()->getIf("id") )
        {
            throw new \Exception("Category ID not set!");
        }
        $id = $this->param()->getInt("id");

        if ($this->param()->postIf("news_save"))
        {
            $name = $this->param()->post("news_name");

            $statement = $this->prepare(
                "UPDATE
                    ZCMS_PREFIX_news_category
                SET 
                    `name` = :name
                WHERE
                    id = :id");

            $statement->bindParam(":name", $name);
            $statement->bindParam(":id", $id);
            $statement->execute();

            $this->module()->core()->redirectAction("news", "catshow");
        }

        $stmt = $this->prepare("SELECT `name` AS `NAME` FROM ZCMS_PREFIX_news_category WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $cat = $stmt->fetch();

        $this->assign("NAME", $cat["NAME"]);
        $this->assign("ID", $id);

        $this->render("catadd_edit");
    }
}
