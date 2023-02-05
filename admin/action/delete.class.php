<?php

namespace apexx\modules\news\admin\action;

class Delete extends \apexx\modules\core\IAction
{
    public function execute(): void
    {
        // Save new content
        if ($this->param()->getIf("id"))
        {
            $id = $this->param()->getInt("id");

            $statement = $this->prepare("DELETE FROM APEXX_PREFIX_content WHERE `id` = :id");
            $statement->bindParam(":id", $id);
            if ($statement->execute())
                $this->module()->core()->redirectAction("content", "index");
            else
                new \Exception("Can not delete content page!");
        }
        else
            new \Exception("Can not delete content page (Id missing)!");
    }
}
