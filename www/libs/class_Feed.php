<?php
###################
## Feed Settings ##
###################

/* FSCode Replacement
 *
 * Put FSCodes which should be converted to html into $to_html
 * Put FSCodes which should be converted to text into $to_text
 * All the other FSCodes won't be touched at all
 *
 * List of possible FSCodes:
 *
 * b, i, u, s, center, url, home, email, img, cimg, list, numlist,
 * font, color, size, code, quote, video, noparse, smilies
 *
 * $to_html = array('b', 'i', 'u', 's', 'center', 'url', 'home', 'email', 'list', 'numlist');
 * $to_text = array('img', 'cimg', 'font', 'color', 'size', 'code', 'quote', 'video', 'noparse');
 * $to_bbcode = array();
 *
 * */

/* Shortening Feed Content
 *
 * Set $shortening = x to cut the News after x chars (including html and fscode).
 * $extension will be added at the end of the content.
 *
 * Set $shortening = false if you don't want to cut the content.
 * $extensiion will be ignored.
 *
 * $shortening = false;
 * $extension = "...";
 *
 * */

/* Use HTML
 *
 * Set $use_html = true or false, wheter you want to use HTML in your feed or not.
 * Using HTML is slightly against the RSS-Standard, but mostly all
 * Feed-Readers will show it nevertheless.
 *
 * Note: If you convert some FSCodes to HTML, this HTML will be also removed.
 *
 * $use_html = true;
 *
 * */

/* Template Functions
 *
 * Allowed values: preserve, remove, softpreserve, softremove
 *
 * Most template functions can't be used within feeds (e.g. applets).
 * So they won't be converted and should be removed.
 *
 * preserve = don' convert, don't remove text representation
 * remove   = don' convert, remove text representation
 * soft*    = convert if possible, preserve or remove others
 *
 * $tpl_functions = "softremove";
 *
 * */

/* FSCode Options
 *
 * This settings will be forwarded to the FSCode-Parser
 *
 * $paragraph_to_text = false;
 * $tab = "nbsp";
 *
 * */

##################
## Settings End ##
##################


abstract class Feed {

    private $settings = array (
        'to_html' => array('b', 'i', 'u', 's', 'center', 'url', 'home', 'email', 'list', 'numlist'),
        'to_text' => array('img', 'cimg', 'font', 'color', 'size', 'code', 'quote', 'video', 'noparse'),
        'to_bbcode' => array(),
        'truncate' => false,
        'truncate_extension' => '',
        'truncate_awareness' => array('word' => true, 'html' => true, 'bbcode' => false),
        'truncate_options' => array('count_html' => false, 'count_bbcode' => false, 'below' => true),
        'use_html' => true,
        'tpl_functions' => 'softremove',
        'paragraph_to_text' => false,
        'tab' => 'nbsp'
    );

    private $title;
    private $fsUrl;
    private $description;
    private $language;

    private $lastUpdate;
    private $feedUrl;

    private $items = array();


    /**
     * Constructor of class Feed.
     *
     * @return void
     */
    public function __construct($feedUrl, $settings = array()) {
        // set feed Url
        $this->feedUrl = $feedUrl;

        // set settings
        $this->setSettings($settings);
    }

    // Set Settings
    private function setSettings($settings = array()) {
        foreach ($settings as $setting => $value) {
            if (isset($this->settings[$setting]))
                $this->settings[$setting] = $value;
        }
    }

    // Load Data from DB and parse it
    private function loadData() {
        global $FD;

        // Include functions & libs
        require_once(FS2_ROOT_PATH . 'includes/fscode.php');
        require_once(FS2_ROOT_PATH . 'includes/indexfunctions.php');

        // Load virtualhost
        if (is_empty($virtualhost = $FD->cfg('virtualhost'))) {
            $virtualhost = 'http://'.$_SERVER['HTTP_HOST'];
        }

        // Set Header Data
        $this->title = $FD->cfg('title');
        $this->fsUrl = $virtualhost;
        $this->description = $FD->cfg('description');
        $this->language = $FD->cfg('language');
        $this->lastUpdate = 0;

        // News Config + Infos
        $config_arr = $FD->sql()->getById('news_config', array('num_news'), 1);

        // Get News from DB
        $news_arr = $FD->sql()->getData('news', array('news_id', 'news_text', 'news_title', 'news_date', 'user_id'), array(
            'W' => '`news_date` <= '.$FD->env('time').' AND `news_active` = 1',
            'O' => '`news_date` DESC',
            'L' => $config_arr['num_news'],
        ));

        // Parse News items
        foreach ($news_arr as $news) {

            // check for latest news_date
            if ($news['news_date'] > $this->lastUpdate)
                $this->lastUpdate = $news['news_date'];

            // get user name
            $news['user_name'] = $FD->sql()->getFieldById('user', 'user_name', $news['user_id'], 'user_id');

            // parse fscode in news
            $flags = array(
                'html' => $this->settings['use_html'],
                'paragraph' => true,
                'paragraph_to_text' => $this->settings['paragraph_to_text'],
                'tab' => $this->settings['tab'],
                'tabsize' => 8,
                'full_urls' => true
            );
            $parsed_text = parse_fscode($news['news_text'], $flags, $this->settings['to_html'], $this->settings['to_text'], $this->settings['to_bbcode']);

            // tpl_funcions
            switch ($this->settings['tpl_functions']) {
                case 'remove':
                    $parsed_text = remove_tpl_functions($parsed_text, get_all_tpl_functions());
                    break;
                case 'softremove':
                    $parsed_text = tpl_functions ($parsed_text, $FD->cfg('system', 'var_loop'), array('DATE', 'VAR', 'URL'), false);
                    $parsed_text = remove_tpl_functions($parsed_text, get_all_tpl_functions());
                    break;
                case 'softpreserve':
                    $parsed_text = tpl_functions ($parsed_text, $FD->cfg('system', 'var_loop'), array('DATE', 'VAR', 'URL'));
                    break;
                default: // preserve
                    break;
            }

            // remove any html?
            if (!$this->settings['use_html'])
                $parsed_text = strip_tags($parsed_text);

            // Cut Text
            if ($this->settings['truncate'] !== false) {
				$parsed_text = StringCutter::truncate($parsed_text, $this->settings['truncate'], $this->settings['truncate_extension'], $this->settings['truncate_awareness'], $this->settings['truncate_options']);
			}

            // Save item
            $news['parsed_text'] = $parsed_text;
            array_push($this->items, $news);
        }
    }

    // Return the Feed as String
    public function __toString () {

        // Load Data from DB and parse
        $this->loadData();

        initstr($ret);

        // Get Header
        $ret = $this->getHeaderXml();

        // Get Items
        foreach ($this->items as $item) {
            $ret .= $this->getItemXml($item);
        }

        // Get Footer
        $ret .= $this->getFooterXml();

        return $ret;
    }

    // Getter
    public function getTitle() {
        return $this->title;
    }
    public function getFsUrl() {
        return $this->fsUrl;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getLanguage() {
        return $this->language;
    }
    public function getLastUpdate() {
        return $this->lastUpdate;
    }
    public function getFeedUrl() {
        return $this->feedUrl;
    }
    public function getItems() {
        return $this->items;
    }
    public function getSettings() {
        return $this->settings;
    }

    // Define abstract methods
    abstract protected function getHeaderXml();
    abstract protected function getItemXml($item);
    abstract protected function getFooterXml();

}

class RSS20 extends Feed {

    // Return Header XML
    protected function getHeaderXml() {
        global $FD;

        return '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <language>'.$this->getLanguage().'</language>
        <description>'.utf8_encode(htmlspecialchars($this->getDescription())).'</description>
        <link>'.utf8_encode($this->getFsUrl()).'</link>
        <title>'.utf8_encode(htmlspecialchars($this->getTitle())).'</title>
        <atom:link href="'.utf8_encode($this->getFeedUrl()).'" rel="self" type="application/rss+xml" />';

    }

    // Return XML Representation of an item
    protected function getItemXml($item) {
        return '
        <item>
            <title>'.utf8_encode(htmlspecialchars($item['news_title'])).'</title>
            <link>'.utf8_encode(url('comments', array('id' => $item['news_id']), true)).'</link>
            <pubDate>'.utf8_encode(date('r', $item['news_date'])).'</pubDate>
            <description><![CDATA['.utf8_encode($item['parsed_text']).']]></description>
            <guid>'.utf8_encode(url('comments', array('id' => $item['news_id']), true)).'</guid>
        </item>';
    }

    // Return Footer XML
    protected function getFooterXml() {
        return '
    </channel>
</rss>';
    }
}

class RSS10 extends Feed {

    // Return Header XML
    protected function getHeaderXml() {
        global $FD;

        // Header
        $ret = '<?xml version="1.0" encoding="utf-8"?>
<rdf:RDF
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns="http://purl.org/rss/1.0/"
>
    <channel rdf:about="'.utf8_encode($this->getFeedUrl()).'">
        <title>'.utf8_encode(htmlspecialchars($this->getTitle())).'</title>
        <link>'.utf8_encode($this->getFsUrl()).'</link>
        <description>'.utf8_encode(htmlspecialchars($this->getDescription())).'</description>
        <items>
            <rdf:Seq>';

        // Itemlist in Header
        foreach ($this->getItems() as $item) {
            $ret .= '
                <rdf:li resource="'.utf8_encode(url('comments', array('id' => $item['news_id']), true)).'" />';
        }

        // Last part of header
        $ret .= '
            </rdf:Seq>
        </items>
    </channel>';

        return $ret;
    }

    // Return XML Representation of an item
    protected function getItemXml($item) {
        return '
    <item rdf:about="'.utf8_encode(url('comments', array('id' => $item['news_id']), true)).'">
        <title>'.utf8_encode(htmlspecialchars($item['news_title'])).'</title>
        <link>'.utf8_encode(url('comments', array('id' => $item['news_id']), true)).'</link>
        <description><![CDATA['.utf8_encode($item['parsed_text']).']]></description>
    </item>';
    }

    // Return Footer XML
    protected function getFooterXml() {
        return '
</rdf:RDF>';
    }
}

class RSS091 extends Feed {

    // Return Header XML
    protected function getHeaderXml() {
        global $FD;

        return '<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 0.91//EN" "http://www.rssboard.org/rss-0.91.dtd">
<rss version="0.91">
    <channel>
        <language>'.$this->getLanguage().'</language>
        <description>'.utf8_encode(htmlspecialchars($this->getDescription())).'</description>
        <link>'.utf8_encode($this->getFsUrl()).'</link>
        <title>'.utf8_encode(htmlspecialchars($this->getTitle())).'</title>';
    }

    // Return XML Representation of an item
    protected function getItemXml($item) {
        return '
        <item>
            <title>'.utf8_encode(htmlspecialchars($item['news_title'])).'</title>
            <link>'.utf8_encode(url('comments', array('id' => $item['news_id']), true)).'</link>
            <pubDate>'.utf8_encode(date('r', $item['news_date'])).'</pubDate>
            <description><![CDATA['.utf8_encode($item['parsed_text']).']]></description>
        </item>';
    }

    // Return Footer XML
    protected function getFooterXml() {
        return '
    </channel>
</rss>';
    }
}

class Atom10 extends Feed {

    // Return Header XML
    protected function getHeaderXml() {
        global $FD;

        // format last_update date
        $last_updated = date("Y-m-d\TH:i:s", $this->getLastUpdate()).'Z';

        return '<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <id>'.utf8_encode($this->getFsUrl()).'</id>
    <title>'.utf8_encode(htmlspecialchars($this->getTitle())).'</title>
    <updated>'.utf8_encode($last_updated).'</updated>
    <link rel="self" href="'.utf8_encode($this->getFeedUrl()).'" />';
    }

    // Return XML Representation of an item
    protected function getItemXml($item) {

        //html content?
        $settings = $this->getSettings();
        $html_content = ($settings['use_html'] ? ' type="html"' : '');

        // format news date
        $news_updated = date("Y-m-d\TH:i:s", $item['news_date']).'Z';

        return '
    <entry>
        <id>'.utf8_encode(url('comments', array('id' => $item['news_id']), true)).'</id>
        <title>'.utf8_encode(htmlspecialchars($item['news_title'])).'</title>
        <updated>'.utf8_encode($news_updated).'</updated>
        <author>
            <name>'.utf8_encode(htmlspecialchars($item['user_name'])).'</name>
        </author>
        <content'.$html_content.'><![CDATA['.utf8_encode($item['parsed_text']).']]></content>
        <link rel="alternate" href="'.utf8_encode(url('comments', array('id' => $item['news_id']), true)).'" />
    </entry>';
    }

    // Return Footer XML
    protected function getFooterXml() {
        return '
</feed>';
    }
}
?>
