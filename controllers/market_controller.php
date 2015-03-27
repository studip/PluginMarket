<?php
require_once 'app/controllers/plugin_controller.php';

class MarketController extends PluginController
{
    public function absolute_url_for($to)
    {
        $old_base = URLHelper::setBaseURL($GLOBALS['ABSOLUTE_URI_STUDIP']);

        $args = func_get_args();
        $url  = call_user_func_array(array($this, 'url_for'), $args);

        URLHelper::setBaseURL($old_base);

        return $url;
    }

    /**
     * Converts a given string to our xml friendly text.
     * This step involves purifying the string
     *
     * @param String $string Input string to reformat
     * @return String Reformatted string (optional HTML -> Markdown, UTF-8)
     */
    public function xml_ready($string, $convert_to_markdown = true)
    {
        static $purifier = null;
        static $fixer = null;
        static $markdown = null;

        if ($purifier === null) {
            $purifier_config = HTMLPurifier_Config::createDefault();
            $purifier_config->set('Cache.SerializerPath', realpath($GLOBALS['TMP_PATH']));
            $purifier = new HTMLPurifier($purifier_config);

            $markdown = new HTML_To_Markdown();
            $markdown->set_option('strip_tags', true);
        }

        $string = studip_utf8encode($string);

        $string = $purifier->purify($string);
        if ($convert_to_markdown) {
            $string = $markdown->convert($string);

            $string = preg_replace('/\[\]\((\w+:\/\/.*?)\)/', '', $string);

            $string = preg_replace('/\[(\w+:\/\/.*?)\/?\]\(\\1\/?\s+"(.*?)"\)/isxm', '$2: $1', $string);
            $string = preg_replace('/\[(\w+:\/\/.*?)\/?\]\(\\1\/?\)/isxm', '$1', $string);
            $string = preg_replace('/\[(.*?)\]\((\w+:\/\/.*?)\)/', '$1: $2', $string);
        }

        $string = preg_replace('/[\x00-\x08\x0b\x0c\x0e-\x1f]/', '', $string);
        $string = trim($string);

        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');

        return $string;
    }

    public function create_xml_element($parent, $name, $value, $attributes = array(), $convert_to_markdown = true)
    {
        $element = $parent->createElement($name, $this->xml_ready($value, $convert_to_markdown));
        foreach ($attributes as $k => $v) {
            $element->setAttribute($k, $this->xml_ready($v, $convert_to_markdown));
        }
        return $element;
    }
}
