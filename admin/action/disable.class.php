<?php

namespace apexx\modules\news\admin\action;

class Disable extends \apexx\modules\core\IAction
{
    public function execute(): void
    {
        // Save new news
        if ($this->param()->getIf("id"))
        {
            $id = $this->param()->getInt("id");

            $statement = $this->prepare("UPDATE APEXX_PREFIX_news SET `time` = NULL WHERE `id` = :id");
            $statement->bindParam(":id", $id);
            if ($statement->execute())
                $this->module()->core()->redirectAction("news", "index");
            else
                var_dump($statement->errorInfo());
        }
        else
            new \Exception("Can not enable news page (Id missing)!");
    }
}
