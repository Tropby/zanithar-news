<?php

namespace apexx\modules\news;

use apexx\modules\core\EXECUTION_TYPE;

class Setup extends \apexx\modules\core\ISetup
{
    public function install(): bool
    {
        $ok = true;

        $ok &= $this->executeSQL("CREATE TABLE `APEXX_PREFIX_news` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

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

        $ok &= $this->executeSQL("DROP TABLE `APEXX_PREFIX_news`");

        if (!$ok)
            return false;

        return true;
    }
}
