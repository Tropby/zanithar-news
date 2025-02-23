<?php

namespace zanithar\modules\news;

use zanithar\modules\core\EXECUTION_TYPE;

class Setup extends \zanithar\modules\core\ISetup
{
    public function install(): bool
    {
        $ok = true;

        $ok &= $this->executeSQL("CREATE TABLE `ZCMS_PREFIX_news` (
            `id` int(11) UNSIGNED NOT NULL,
            `secid` tinytext NOT NULL,
            `prodid` int(11) UNSIGNED NOT NULL DEFAULT 0,
            `catid` int(11) UNSIGNED NOT NULL DEFAULT 0,
            `userid` int(11) UNSIGNED NOT NULL DEFAULT 0,
            `send_username` tinytext NOT NULL,
            `send_email` tinytext NOT NULL,
            `send_ip` tinytext NOT NULL,
            `newspic` tinytext NOT NULL,
            `title` tinytext NOT NULL,
            `subtitle` tinytext NOT NULL,
            `teaser` text NOT NULL,
            `text` mediumtext NOT NULL,
            `galid` int(11) UNSIGNED NOT NULL DEFAULT 0,
            `meta_description` text NOT NULL,
            `links` text NOT NULL,
            `addtime` int(11) UNSIGNED NOT NULL DEFAULT 0,
            `starttime` int(11) UNSIGNED NOT NULL DEFAULT 0,
            `endtime` int(11) UNSIGNED NOT NULL DEFAULT 0,
            `top` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
            `sticky` int(11) UNSIGNED NOT NULL DEFAULT 0,
            `searchable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
            `allowcoms` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
            `allowrating` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
            `restricted` tinyint(1) UNSIGNED NOT NULL,
            `hits` int(11) UNSIGNED NOT NULL DEFAULT 0
            ) DEFAULT CHARSET=utf8mb4;");

        $ok &= $this->executeSQL("CREATE TABLE `ZCMS_PREFIX_news_category` (
                `id` int NOT NULL,
                `name` varchar(64) NOT NULL
            ) DEFAULT CHARSET=utf8mb4;");
  
        $ok &= $this->executeSQL("ALTER TABLE `ZCMS_PREFIX_news_category` ADD PRIMARY KEY (`id`);");
        $ok &= $this->executeSQL("ALTER TABLE `ZCMS_PREFIX_news_category` MODIFY `id` int NOT NULL AUTO_INCREMENT;");

        $ok &= $this->executeSQL("CREATE TABLE `ZCMS_PREFIX_news_receivers` (
            `id` int NOT NULL,
            `email` varchar(128) CHARACTER SET utf8mb4 NOT NULL,
            `category` int NOT NULL,
            `add_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=ddInnoDB DEFAULT CHARSET=utf8mb4;");

        $ok &= $this->executeSQL("ALTER TABLE `ZCMS_PREFIX_news_receivers`
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE KEY `e-mail` (`email`,`category`),
            ADD KEY `category` (`category`);");

        $ok &= $this->executeSQL("ALTER TABLE `ZCMS_PREFIX_news_receivers` MODIFY `id` int NOT NULL AUTO_INCREMENT;");
        $ok &= $this->executeSQL("ALTER TABLE `ZCMS_PREFIX_news_receivers` ADD CONSTRAINT `ZCMS_PREFIX_news_receivers_ibfk_1` FOREIGN KEY (`category`) REFERENCES `ZCMS_PREFIX_news_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        if( !$ok ) 
            return false;

        return true;
    }

    public function update($currentVersion): bool
    {
        return true;
    }

    public function uninstall(): bool
    {
        $ok = true;

        $ok &= $this->executeSQL("DROP TABLE `ZCMS_PREFIX_news`");

        if (!$ok)
            return false;

        return true;
    }
}
