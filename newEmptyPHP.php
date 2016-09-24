<?php
error_reporting(E_ALL);
ini_set("display_error", true);

class XPath
{

    private $_doc;
    private $_xpath;

    /**
     * 
     * @param string $content
     * @param boolean $html Parse text as html
     */
    public function __construct($content, $html = false)
    {
        $this->_doc = new \DOMDocument();
        if ($html) {
            @$this->_doc->loadHTML($content);
        } else {
            $this->_doc->loadXML($content);
        }
        $this->_xpath = new \DOMXpath($this->_doc);
    }

    public static function isAssociative($array)
    {
        return !empty($array) && (array_keys($array) !== range(0, count($array) - 1));
    }

    /**
     * 
     * @param \DOMNodeList $elements
     * @return mixed|NULL
     */
    public static function xmlToArray($elements)
    {
        if ($elements instanceof \DOMNodeList) {
            if ($elements->length == 0) {
                return null;
            } elseif ($elements->length == 1) {
                return self::xmlToArray($elements->item(0));
            } else {
                $result = [];
                foreach ($elements as $element) {
                    $result[] = self::xmlToArray($element);
                }
                return $result;
            }
        } elseif ($elements instanceof \DOMNode) {
            if ($elements->hasChildNodes()) {
                $result = [];
                foreach ($elements->childNodes as $element) {
                    if ($element->nodeType != 3) {
                        if (isset($result[$element->nodeName])) {
                            if (is_array($result[$element->nodeName]) && !self::isAssociative($result[$element->nodeName])) {
                                $result[$element->nodeName][] = self::xmlToArray($element);
                            } else {
                                $v = $result[$element->nodeName];
                                $result[$element->nodeName] = [];
                                $result[$element->nodeName][] = $v;
                                $result[$element->nodeName][] = self::xmlToArray($element);
                            }
                        } else {
                            $result[$element->nodeName] = self::xmlToArray($element);
                        }
                    }
                }
                if (count($result) == 0) {
                    return $elements->nodeValue;
                } else {
                    return $result;
                }
            } else {
                return $elements->nodeValue;
            }
        }
    }

    /**
     * 
     * @param array $paths
     * @param \DOMNode $contextNode
     * @param boolean $assoc
     * @return array
     */
    public function queryAll($paths, $contextNode = null, $assoc = true)
    {
        $result = [];
        foreach ($paths as $name => $path) {
            $elements = $this->_xpath->query($path, $contextNode);
            if ($assoc) {
                $result[$name] = self::xmlToArray($elements);
            } else {
                $result[$name] = $elements;
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $path
     * @param \DOMNode $contextNode
     * @param boolean $assoc
     * @return array|\DOMNodeList|NULL
     */
    public function query($path, $contextNode = null, $assoc = true)
    {
        $elements = $this->_xpath->query($path, $contextNode);
        if ($elements->length > 0) {
            if ($assoc) {
                return self::xmlToArray($elements);
            } else {
                return $elements;
            }
        }
        return null;
    }

    /**
     * 
     * @param string $path
     * @param \DOMNode $contextNode
     * @param boolean $assoc
     * @return array|\DOMNode|NULL
     */
    public function queryOne($path, $contextNode = null, $assoc = true)
    {
        $elements = $this->_xpath->query($path, $contextNode);
        if ($elements->length > 0) {
            $el = $elements->item(0);
            if ($assoc) {
                return self::xmlToArray($el);
            } else {
                return $el;
            }
        }
        return null;
    }

    /**
     * 
     * @param \DOMNode $node
     * @return integer
     */
    public function getNodePos($node)
    {
        $prevSibling = $node->previousSibling;
        $pos = 1;
        while (!empty($prevSibling)) {
            $prevSibling = $prevSibling->previousSibling;
            $pos++;
        }
        return $pos;
    }

    /**
     * 
     * @param string $path XPath
     * @param \DOMNode $contextNode
     * @throws CException
     * @return integer|NULL
     */
    public function findPos($path, $contextNode = null)
    {
        try {
            $elements = $this->_xpath->query($path, $contextNode);
            if ($elements->length > 0) {
                if ($elements->length == 1) {
                    return $this->getNodePos($elements->item(0));
                } else {
                    $result = [];
                    foreach ($elements as $element) {
                        $result[] = $this->getNodePos($element);
                    }
                    return $result;
                }
            }
            return null;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' : ' . $path);
        }
    }

    /**
     * 
     * @param array $paths
     * @param \DOMNode $contextNode
     * @return array
     */
    public function findPosAll($paths, $contextNode = null)
    {
        $result = [];
        foreach ($paths as $key => $path) {
            $result[$key] = $this->findPos($path, $contextNode);
        }
        return $result;
    }

    /**
     * 
     * @param string $path
     * @param \DOMNode $contextNode
     * @return NULL|mixed
     */
    public function evalute($path, $contextNode = null)
    {
        $entries = $this->_xpath->evaluate($path, $contextNode);
        if (is_a($entries, 'DOMNodeList'))
            if ($entries->length > 0) {
                return $entries->item(0)->nodeValue;
            } else {
                return null;
            }
        return $entries ? : null;
    }

    /**
     * @return \DOMXpath
     */
    public function getXPath()
    {
        return $this->_xpath;
    }

    /**
     * Recursive clears all text in array
     * @param string $value
     * @return string|NULL
     */
    public static function clearTextConcat($value)
    {
        if (is_string($value)) {
            return trim(preg_replace('/\s+/s', ' ', $value));
        }
        if (is_array($value)) {
            $result = [];
            foreach ($value as $val) {
                $result[] = self::clearTextConcat($val);
            }
            return implode(' ', array_filter($result, 'strlen'));
        }
        return null;
    }

    /**
     * Clears text
     * @param string $value
     */
    public static function clearText(&$value)
    {
        if (is_string($value)) {
            $value = trim(preg_replace('/\s+/s', ' ', $value));
        }
        if (is_array($value)) {
            foreach ($value as &$val) {
                self::clearText($val);
            }
        }
    }

    /**
     * 
     * @return \DOMDocument
     */
    public function getDoc()
    {
        return $this->_doc;
    }

    public function registerNamespace($prefix, $namespaceURI)
    {
        $this->_xpath->registerNamespace($prefix, $namespaceURI);
    }

    /**
     * 
     * @param string $path
     * @param string $value
     * @param \DOMNode $contextNode
     * @return boolean
     */
    public function updateOne($path, $value, $contextNode = null)
    {
        $elements = $this->_xpath->query($path, $contextNode);
        if ($elements->length > 0) {
            $el = $elements->item(0);
            $el->nodeValue = $value;
            return true;
        }
        return false;
    }

    /**
     * 
     * @param string $path
     * @param string $value
     * @param \DOMNode $contextNode
     * @return boolean
     */
    public function update($path, $value, $contextNode = null)
    {
        $elements = $this->_xpath->query($path, $contextNode);
        if ($elements->length > 0) {
            foreach ($elements as $el) {
                $el->nodeValue = $value;
            }
            return true;
        }
        return false;
    }

    /**
     * 
     * @param array $paths [XPath => Value]
     * @param string $contextNode
     * @return integer
     */
    public function updateAll($paths, $contextNode = null)
    {
        $result = 0;
        foreach ($paths as $path => $value) {
            $elements = $this->_xpath->query($path, $contextNode);
            if ($elements->length > 0) {
                foreach ($elements as $el) {
                    $el->nodeValue = $value;
                    $result++;
                }
            }
        }
        return $result;
    }
}

function getData($uid)
{
    $login = 'efin2012@yandex.ru'; //Логин
    $passwd = 'gf20hj12km777'; //Пароль
    $user_cookie_file = $_SERVER['DOCUMENT_ROOT'] . '/cookies.txt';
    $idkey = '132840909'; //Фиг знает что
    $url = 'https://mail.yandex.ru/?uid=' . $uid . '&login=efin2012';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
    curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "from=passport&idkey=$idkey&display=page&login=$login&passwd=$passwd");
    $headerText = curl_exec($ch);
    if (!$headerText) {
        $error = curl_error($ch) . '(' . curl_errno($ch) . ')';
        return "Ошибка:" . $error;
    } else {
        return $headerText;
    }
    curl_close($ch);
}

function auth($url)
{
    $url = "https://passport.yandex.ru/passport?mode=auth";
    $login = 'efin2012@yandex.ru'; //Логин
    $passwd = 'aUBEA4VgKpSN'; //Пароль
    $user_cookie_file = $_SERVER['DOCUMENT_ROOT'] . '/cookies.txt';
    $idkey = '132840909';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
    curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "from=passport&idkey=$idkey&display=page&login=$login&passwd=$passwd");
    $headerText = curl_exec($ch);
    if (!$headerText) {
        $error = curl_error($ch) . '(' . curl_errno($ch) . ')';
        return "Ошибка:" . $error;
    } else {
        return $headerText;
    }
    curl_close($ch);
}
//echo '------------------- <pre/> ---------------------------------------------------------';
$headerText = auth($url);
preg_match('/Session_id=(.*)/', $headerText, $m);

$data = explode('.', $m[1]);
$data2 = explode('|', $data[4]);
$uid = $data2[1];


$html = getData($uid);

file_put_contents('html_yandex.ru', $html);

$xpath = new XPath($html, true);
$emailList['from'] = $xpath->query('//*[@id="main"]/div/div/span[2]/a[1]/span/span/text()');
$emailList['header'] = $xpath->query('//*[@id="main"]/div/div/span[2]/a[2]/span/span/text()');
$emailList['date'] = $xpath->query('//*[@id="main"]/div/div/span[1]/span/text()');


for ($i = 0; $i <= count($emailList['from']); $i++) {
    if (preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $emailList['from'][$i], $match)) {
        $emailList['from'][$i] = $match[0];
    }
}
echo '<pre>';
var_dump($emailList, '$emailList');
exit;
