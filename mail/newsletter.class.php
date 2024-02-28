<?php

namespace apexx\modules\news\mail;

class Newsletter extends \apexx\modules\core\IMail 
{
    public function __construct(\apexx\modules\core\IModule $module)
    {        
        parent::__construct($module);
    }

    public function execute(array $data): void 
    {
        $this->assign("MAIL_TITLE", $data["TITLE"]);
        $this->assign("ID", (int)$data["ID"]);
        $this->assign("MAIL_TEASER", $data["TEXT"]);
        
        $this->setSubject( $this->module()->core()->dbConfigValue("main", "websitename")." - Newsletter - ".$data["TITLE"]);

        $category = (int)$data["CATEGORY"];

        $db = $this->module()->core()->db();
        $stmt = $db->prepare("SELECT `email` AS EMAIL FROM APEXX_PREFIX_news_receivers WHERE category = :category");
        $stmt->bindParam(":category", $category);
        $stmt->execute();

        // TODO: Move the newsletter sender to cronjob, to prevent timeouts
        $emails = $stmt->fetchAll();
        foreach( $emails as $email )
        {
            $this->clearReceiver();
            $this->addReceiver($email["EMAIL"]);
            $this->send("newsletter");
        }
    }
}
