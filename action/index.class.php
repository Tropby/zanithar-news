<?php

namespace zanithar\modules\news\action;

use zanithar\modules\core\EXECUTION_TYPE;
use zanithar\modules\core\Pager;

class Index extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        /*** @var \zanithar\modules\user\Module */
        $userModule = $this->module()->core()->module("user");
        $user = $userModule->currentUser();

        $pager = new Pager($this->module()->core(),
            "a.id AS ID, UNIX_TIMESTAMP(`time`) AS `TIME`, a.title as TITLE, `text` AS `TEXT`, b.name AS CAT_NAME",
            "ZCMS_PREFIX_news AS a LEFT JOIN ZCMS_PREFIX_news_category AS b ON (a.category = b.id)",
                ( $this->param()->getIf("catid") ? "b.id = " . $this->param()->getInt("catid")." AND " : "" ). " " .
                ($user->hasRight("news", "index", EXECUTION_TYPE::ADMIN) ? "" :
                "  UNIX_TIMESTAMP(`time`) < ".time()." AND `time` IS NOT NULL AND ") . "1=1",
            "`time` IS NULL  DESC, `time` DESC",
            20, []
        );

        $this->assign("CURRENT_TIME", time());

        $lists = $pager->prepare();
        $pages = $pager->pages();
        $this->assign("PAGES", $pages);
        $this->assign("NEWS", $lists);

        if( $this->param()->getIf("catid") )
        {
            $catid = $this->param()->getInt("catid");
            $stmt = $this->prepare("SELECT `name` AS `NAME` FROM ZCMS_PREFIX_news_category WHERE id = :id");
            $stmt->bindParam(":id", $catid);
            $stmt->execute();
            $this->assign("CAT_NAME", $stmt->fetch()["NAME"]);
        }
        else
        {
            $this->assign("CAT_NAME", null);
        }

        $this->render("index");
    }
}
