<?php
/**
 * Breadcrumb (Deluxe) Plugin: a replacement for the internal breadcumb
 *
 * @license  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author   Bjoern Ellebrecht <dokuwiki.development@c0de8.com>
 *
 * @noinspection PhpUnused,
 *               PhpMissingParamTypeInspection, PhpMissingReturnTypeInspection
 */

// must be run within Dokuwiki
use dokuwiki\File\PageResolver;

if(!defined('DOKU_INC')) {
    die();
}

class syntax_plugin_breadcrumbdeluxe extends DokuWiki_Syntax_Plugin
{
    final public function getType(): string
    {
        return 'substition';
    }

    final public function getPType(): string {
        return 'normal';
    }

    final public function getSort(): string {
        return 1;
    }

    final public function handle($match, $state, $pos, Doku_Handler $handler): bool
    {
        return true;
    }

    final public function render($format, Doku_Renderer $renderer, $data): void
    {
    }

    /**
     * Hierarchical breadcrumbs (Deluxe))  {copied from original dokuwiki and modified}
     *
     * This code was suggested as replacement for the usual breadcrumbs.
     * It only makes sense with a deep site structure.
     *
     * @param ?string $separator Separator between entries
     * @param bool $return return or print
     * @return bool|string
     */
    final public function tpl_youarehere(?string $separator = null, bool $return = false) {
        global $conf;
        global $ID;
        global $lang;

        // check if enabled
        if ($this->isBreadcrumbDisabled()) {
            return false;
        }

        // set default
        if ($separator === null) {
            $separator = ' » ';
        }

        $out = '';

        $parts = explode(':', $ID);
        $count = count($parts);

        // display label (you are here) for breadcrumb
        $out .= '<span class="bchead">' . $lang['youarehere'] . ' </span>';

        // always print the startpage
        $out .= '<span class="home">' . tpl_pagelink(':' . ucfirst($conf['start']), null, true) . '</span>';

        // print intermediate namespace links
        $part = '';
        for ($i = 0; $i < $count - 1; $i++) {
            $part .= $parts[$i] . ':';
            $page = $part;
            if ($page == $conf['start']) continue; // Skip startpage

            // output
            $out .= $separator . tpl_pagelink($page, p_get_first_heading($parts[$i]), true);
        }

        // print current page, skipping start page, skipping for namespace index
        if (isset($page)) {
            $page = (new PageResolver('root'))->resolveId($page);
            if ($page == $part . $parts[$i]) {
                if ($return) return $out;
                echo $out;
                return true;
            }
        }
        $page = $part . $parts[$i];
        if ($page == $conf['start']) {
            if ($return) return $out;
            echo $out;
            return true;
        }

        $out .= $separator;
        $out .= tpl_pagelink($page, p_get_first_heading($page), true);

        if ($return) {
            return $out;
        }

        echo $out;

        return (bool)$out;
    }

    private function isBreadcrumbDisabled(): bool
    {
        global $conf;

        return (bool) !$conf['youarehere'];
    }
}
