<?php

namespace zanithar\modules\news\admin\action;

use zanithar\modules\core\EXECUTION_TYPE;

class Add extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        // Save new news
        if ($this->param()->postIf("news_save"))
        {
            $news = $this->param()->post("news_text");
            $title = $this->param()->post("news_title");
            $secid = "all";
            $category = $this->param()->post("news_catid");
            $userid = $this->param()->post("news_userid");
            //$metaDescription = $this->param()->post("news_metaDescription");
            $time = time();
            $lastchange = time();

            /**
             * @var \zanithar\modules\user\Module
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
                    zanithar_PREFIX_news 
                    ( 
                        `text`, 
                        `title`, 
                        `author`,
                        `time`,
                        `lastchange`,
                        `lastchange_userid`,
                        `searchable`,
                        `allowcoms`,
                        `allowrating`,
                        `category`
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
                        :category
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
            $statement->bindParam(":category", $category);

            if ($statement->execute())
            {
                if( $this->param()->postIf("news_newsletter") )
                {
                    $id = $this->lastInsertId();
                    $mail = new \zanithar\modules\news\mail\Newsletter($this->module());
                    $mail->execute(
                        [
                            "TITLE" => "$title",
                            "TEXT" => $news,
                            "ID" => $id,
                            "CATEGORY" => $category
                        ]
                    );
    
                }

                $this->module()->core()->redirectAction("news", "index");
            }
            else
                new \Exception(implode("\n", $statement->errorInfo()));
        }

        /// TODO: Check if user is Team-Member
        $stmt = $this->prepare("SELECT * FROM zanithar_PREFIX_user ORDER BY username ASC");
        $stmt->execute();
        $users = $stmt->fetchAll();
        $tmplUsers = array();

        /*** @var \zanithar\modules\user\Module */
        $userModule = $this->module()->core()->module("user");
        /*** @var \zanithar\modules\user\User */
        $userId = $userModule->currentUser()->id();
        foreach ($users as $user)
        {
            $tmplUsers[] = array(
                "VALUE" => $user["userid"],
                "NAME" => $user["username"],
                "SELECTED" => $user["userid"] == $userId
            );
        }

        $stmt = $this->prepare("SELECT * FROM zanithar_PREFIX_news_category ORDER BY `name` ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll();
        $tmplCategories = array();
        foreach ($categories as $category)
        {
            $tmplCategories[] = array(
                "VALUE" => $category["id"],
                "NAME" => $category["name"],
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
        $this->assign("NEWSLETTER", false);

        $this->render("add_edit");
    }
}
