<?php

namespace zanithar\modules\news\admin\action;

use zanithar\modules\core\EXECUTION_TYPE;

use Exception;

class Edit extends \zanithar\modules\core\IAction
{
    public function execute(): void
    {
        if($this->param()->getIf("id"))
            $id = $this->param()->getInt("id");
        else
            new Exception("Can not edit news without ID!");

        // Save new news
        if( $this->param()->postIf("news_save") )
        {
            $text = $this->param()->post("news_text");
            $title = $this->param()->post("news_title");
            $author = $this->param()->post("news_userid");
            $category = $this->param()->post("news_catid");
            $lastchange = time();

            /**
             * @var \zanithar\modules\user\Module
             */
            $userModule = $this->module()->core()->module("user");
            $user = $userModule->currentUser();
            $lastchangeUserid = $user->id();

            if( $this->module()->core()->callFunction("dateTimeInputActive", [ "news_time"]) )
            {
                $time = $this->module()->core()->callFunction("dateTimeInputToUnixTime", [ "news_time"], EXECUTION_TYPE::PUBLIC);
            }
            else
            {
                $time = null;
            }

            $searchable = $this->param()->postIf("news_searchable") ? 1 : 0;
            $allowcoms = $this->param()->postIf("news_allowcoms") ? 1 : 0;
            $allowrating = $this->param()->postIf("news_allowrating") ? 1 : 0;

            $statement = $this->prepare("
                UPDATE 
                    ZCMS_PREFIX_news 
                SET 
                    `text` = :text, 
                    `title` = :title,                    
                    `author` = :author,
                    `lastchange` = FROM_UNIXTIME(:lastchange),
                    `lastchange_userid` = :lastchange_userid,
                    `searchable` = :searchable,
                    `allowcoms` = :allowcoms,
                    `time` = FROM_UNIXTIME(:time),
                    `allowrating` = :allowrating,
                    `category` = :category
                WHERE 
                    `id` = :id");

            $statement->bindParam(":id", $id);
            $statement->bindParam(":text", $text);
            $statement->bindParam(":title", $title);
            $statement->bindParam(":time", $time);
            $statement->bindParam(":author", $author);
            $statement->bindParam(":lastchange", $lastchange);
            $statement->bindParam(":lastchange_userid", $lastchangeUserid);
            $statement->bindParam(":searchable", $searchable);
            $statement->bindParam(":allowcoms", $allowcoms);
            $statement->bindParam(":allowrating", $allowrating);
            $statement->bindParam(":category", $category);

            if( $this->param()->postIf("news_newsletter") )
            {
                $mail = new \zanithar\modules\news\mail\Newsletter($this->module());
                $mail->execute(
                    [
                        "TITLE" => "$title",
                        "TEXT" => $text,
                        "ID" => $id,
                        "CATEGORY" => $category
                    ]
                );

            }

            if($statement->execute())
                $this->module()->core()->redirectAction("news","index");
            else
                new Exception(implode("\n", $statement->errorInfo()));
        }

        $statement = $this->prepare("
            SELECT 
                id AS ID, 
                UNIX_TIMESTAMP(`time`) AS `TIME`, 
                title as TITLE, 
                `text` AS `TEXT`,
                `author` AS USERID,
                UNIX_TIMESTAMP(`lastchange`) AS LAST_CHANGE,
                `lastchange_userid` AS LAST_CHANGE_USERID,
                `searchable` AS SEARCHABLE,
                `allowcoms` AS ALLOWCOMS,
                `allowrating` AS ALLOWRATING,
                `category` AS CATID
            FROM 
                ZCMS_PREFIX_news 
            WHERE 
                `id` = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
        $news = $statement->fetch();

        $stmt = $this->prepare("SELECT * FROM ZCMS_PREFIX_user ORDER BY username ASC");
        $stmt->execute();
        $users = $stmt->fetchAll();
        $tmplUsers = array();
        foreach ($users as $user)
        {
            $tmplUsers[] = array(
                "VALUE" => $user["userid"],
                "NAME" => $user["username"],
                "SELECTED" => $user["userid"] == $news["USERID"]
            );
        }

        $stmt = $this->prepare("SELECT * FROM ZCMS_PREFIX_news_category ORDER BY `name` ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll();
        $tmplCategories = array();
        foreach ($categories as $category)
        {
            $tmplCategories[] = array(
                "VALUE" => $category["id"],
                "NAME" => $category["name"],
                "SELECTED" => $category["id"] == $news["CATID"]
            );
        }
        $this->assign("CATS", $tmplCategories);

        $this->assign("ID", $id);
        $this->assign("TITLE", $news["TITLE"]);
        $this->assign("TEXT", $news["TEXT"]);
        $this->assign("TIME", $news["TIME"]);
        $this->assign("USERS", $tmplUsers);
        $this->assign("SEARCHABLE", $news["SEARCHABLE"] != 0);
        $this->assign("ALLOWCOMS", $news["ALLOWCOMS"] != 0);
        $this->assign("ALLOWRATING", $news["ALLOWRATING"] != 0);
        $this->assign("NEWSLETTER", false);

        
        $this->render("add_edit");
    }
}
