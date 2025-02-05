<?php

namespace zanithar\modules\news\admin\action;

class CatDelete extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        // Delete category
        if ($this->param()->getIf("id"))
        {
            $id = $this->param()->getInt("id");

            $statement = $this->prepare("DELETE FROM zanithar_PREFIX_news_category WHERE `id` = :id");
            $statement->bindParam(":id", $id);
            if ($statement->execute())
                $this->module()->core()->redirectAction("news", "catshow");
            else
                new \Exception("Can not delete news category!");
        }
        else
            new \Exception("Can not delete news category (Id missing)!");
    }
}
