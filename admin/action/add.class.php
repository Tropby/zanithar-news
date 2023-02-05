<?php

namespace apexx\modules\news\admin\action;

class Add extends \apexx\modules\core\IAction
{
    public function execute(): void
    {
        // Save new news
        if ($this->param()->postIf("news_save"))
        {
            $news = $this->param()->post("news_text");
            $title = $this->param()->post("news_title");
            $secid = "all";
            $catid = $this->param()->post("news_catid");
            $userid = $this->param()->post("news_userid");
            $metaDescription = $this->param()->post("news_metaDescription");
            $time = time();
            $lastchange = time();

            /**
             * @var \apexx\modules\user\Module
             */
            $userModule = $this->module()->core()->module("user");
            $user = $userModule->currentUser();
            $lastchangeUserid = $user->id();

            $searchable = $this->param()->postIf("news_searchable") ? 1 : 0;
            $allowcoms = $this->param()->postIf("news_allowcoms") ? 1 : 0;
            $allowrating = $this->param()->postIf("news_allowrating") ? 1 : 0;

            if( $this->module()->core()->callFunction("dateTimeInputActive", [ "news_time"]) )
            {
                $time = $this->module()->core()->callFunction("dateTimeInputToUnixTime", [ "news_time"], EXECUTION_TYPE::PUBLIC);
            }
            else
            {
                $time = null;
            }

            $statement = $this->prepare("
                INSERT INTO 
                    APEXX_PREFIX_news 
                    ( 
                        `text`, 
                        `title`, 
                        `userid`,
                        `time`,
                        `lastchange`,
                        `lastchange_userid`,
                        `searchable`,
                        `allowcoms`,
                        `allowrating`,
                        `active`,
                        `time`
                    ) 
                    VALUES 
                    ( 
                        :text, 
                        :title,
                        :userid,
                        FROM_UNIXTIME(:time),
                        FROM_UNIXTIME(:lastchange),
                        :lastchange_userid,
                        :searchable,
                        :allowcoms,
                        :allowrating,
                        0,
                        :time
            )");

            $statement->bindParam(":text", $news);
            $statement->bindParam(":title", $title);
            $statement->bindParam(":userid", $userid);
            $statement->bindParam(":time", $time);
            $statement->bindParam(":lastchange", $lastchange);
            $statement->bindParam(":lastchange_userid", $lastchangeUserid);
            $statement->bindParam(":searchable", $searchable);
            $statement->bindParam(":allowcoms", $allowcoms);
            $statement->bindParam(":allowrating", $allowrating);

            if ($statement->execute())
                $this->module()->core()->redirectAction("news", "index");
            else
                new \Exception(implode("\n", $statement->errorInfo()));
        }

        /// TODO: Check if user is Team-Member
        $stmt = $this->prepare("SELECT * FROM APEXX_PREFIX_user ORDER BY username ASC");
        $stmt->execute();
        $users = $stmt->fetchAll();
        $tmplUsers = array();
        foreach ($users as $user)
        {
            $tmplUsers[] = array(
                "VALUE" => $user["userid"],
                "NAME" => $user["username"],
                "SELECTED" => false
            );
        }

        $stmt = $this->prepare("SELECT `value` FROM APEXX_PREFIX_config WHERE module='news' AND varname='groups'");
        $stmt->execute();
        $categories = unserialize($stmt->fetch()["value"]);
        $tmplCategories = array(array("VALUE" => "-1", "NAME" => "--- Bitte wählen ---", "SELECTED" => true));
        foreach ($categories as $catid => $cat)
        {
            $tmplCategories[] = array(
                    "VALUE" => $catid,
                    "NAME" => $cat,
                    "SELECTED" => false
                );
        }
        $this->assign("CATS", $tmplCategories);
        $this->assign("USERS", $tmplUsers);
        $this->assign("TITLE", "");
        $this->assign("TEXT", "");
        $this->assign("META_DESCRIPTION", "");
        $this->assign("SEARCHABLE", true);
        $this->assign("ALLOWCOMS", false);
        $this->assign("ALLOWRATING", false);

        $this->render("add_edit");
    }
}
