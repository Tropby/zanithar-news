<?php

namespace zanithar\modules\news\admin\action;

class ReceiverDelete extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        $catid = $this->param()->getInt("catid");
        
        // Delete category
        if ($this->param()->getIf("id"))
        {
            $id = $this->param()->getInt("id");

            $statement = $this->prepare("DELETE FROM ZCMS_PREFIX_news_receivers WHERE `id` = :id");
            $statement->bindParam(":id", $id);
            if ($statement->execute())
                $this->module()->core()->redirectAction("news", "receivershow", ["catid" => $catid]);
            else
                new \Exception("Can not delete news category!");
        }
        else
            new \Exception("Can not delete news category (Id missing)!");
    }
}
