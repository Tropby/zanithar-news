<?php

namespace zanithar\modules\news\admin\action;

class Delete extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        // Save new content
        if ($this->param()->getIf("id"))
        {
            $id = $this->param()->getInt("id");

            $statement = $this->prepare("DELETE FROM ZCMS_PREFIX_news WHERE `id` = :id");
            $statement->bindParam(":id", $id);
            if ($statement->execute())
                $this->module()->core()->redirectAction("news", "index");
            else
                new \Exception("Can not delete news!");
        }
        else
            new \Exception("Can not delete news (Id missing)!");
    }
}
