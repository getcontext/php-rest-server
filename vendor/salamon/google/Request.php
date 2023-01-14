<?php
namespace Salamon\Google\ImageReverseSearch;
/**
 *
 * @class Request
 * @author Andrzej Salamon <andrzej.salamon@gmail.com>
 *
 */

class Request
{

    const GOOGLE_URL = 'https://www.google.com';
    const SERVICE_URL = 'https://www.google.com/searchbyimage?image_url=';
    const TAG_ID_RESULTS_COUNT = 'resultStats';
    const TAG_ID_RESULTS = 'rso';
    const ID_CAPTCHA = 'captcha';
    const XPATH_INFRINGEMENT_COMPANY_URL = '//*[@id="rso"]/div[2]/li/div/h3/a[1]';
    const XPATH_INFRINGEMENT_IMAGE_DATA_URL = '//*[@id="rso"]/div[2]/li/div/div/div/div/a';
    const XPATH_NAVIGATION = '//*[@id="navcnt"]/div[2]/li/div/div/div/div/a';
    const XPATH_NAVIGATION_NEXT = '//*[@id="pnnext"]';
    const XPATH_CAPTCHA = '//*[@id="captcha"]';

    /**
     * @var \DOMDocument
     */
    private $dom;

    private $resultCount;

    /**
     * @var Result[]|array
     */
    private $results = array();
    /**
     * @var string source image url
     */
    private $sourceUrl;
    /**
     * @var
     */
    private $nextPageUrl;

    public function __construct($source)
    {
        $this->sourceUrl = $source;
    }


    /**
     * reads results via curl
     *
     */
    public function process()
    {
        $content = $this->send(self::SERVICE_URL . $this->getSourceUrl());

        $this->setDom($content);

        if ($this->isCaptcha()) {
            return false;
        }

        $this->setResultCount();
        $this->addResults();

        while ($this->nextPageUrl != null) {
            $content = $this->send($this->nextPageUrl);
            $this->setDom($content);

            if ($this->isCaptcha()) {
                return false;
            }

            $this->addResults();
        }

        return true;
    }


    /**
     * @return \DOMDocument
     */
    public function getDom()
    {
        return $this->dom;
    }

    /**
     * @param string $content
     */
    public function setDom($content)
    {
        $dom = new \DOMDocument();
        $dom->strictErrorChecking = false; // turn off warnings and errors when parsing
        @$dom->loadHTML($content);
        $this->dom = $dom;
    }

    /**
     * @return mixed
     */
    public function getResultCount()
    {
        return $this->resultCount;
    }

    /**
     *
     */
    private function setResultCount()
    {
        $result = 0;
        $node = $this->getDom()->getElementById("resultStats");
        if ($node) {
            preg_match('/( \d+ )/', $node->nodeValue, $matches);
            $result = isset($matches[0]) ? $matches[0] : 0;
        }
        $this->resultCount = $result;
    }

    /**
     *
     */
    public function isCaptcha()
    {
        $node = $this->getDom()->getElementById(self::ID_CAPTCHA);
        if ($node) {
            // @TODO put CAPTCHA_DETECTED file

            return true;
        }
        return false;
    }

    /**
     * @return Result[]|array
     */
    public function getResults()
    {
        return $this->results;
    }


    private function addResults()
    {
        $dom = $this->getDom();
        $finder = new \DomXPath($dom);

        $nodesUrl = $finder->query(self::XPATH_INFRINGEMENT_COMPANY_URL);
        $nodesImg = $finder->query(self::XPATH_INFRINGEMENT_IMAGE_DATA_URL);

        $stop = $nodesUrl->length;
        for ($i = 0; $i < $stop; $i++) {
            $row = array();
            $nodeUrl = $nodesUrl->item($i);
            $nodeImg = $nodesImg->item($i);

            $infringementPageTitle = $nodeUrl->nodeValue;
            $infringementUrl = null;

            foreach ($nodeUrl->attributes as $x) {
                if ($x->name == 'href') {
                    $infringementUrl = $x->nodeValue;
                }
            }

            $infringementImageDataUrl = null;
            if ($nodeImg) {
                foreach ($nodeImg->attributes as $x) {
                    if ($x->name == 'href') {
                        $infringementImageDataUrl = $x->nodeValue;
                    }
                }
            }

            $row[] = $infringementPageTitle;
            $row[] = $infringementUrl;
            $row[] = $infringementImageDataUrl;

            $this->results[] = $row;
        }

        $nodeNextPage = $finder->query(self::XPATH_NAVIGATION_NEXT)->item(0);
        $nextPageUrl = null;
        if ($nodeNextPage) {
            foreach ($nodeNextPage->attributes as $x) {
                if ($x->name == 'href') {
                    $nextPageUrl = $x->nodeValue;
                    $this->nextPageUrl = self::GOOGLE_URL . $nextPageUrl;
                }
            }
        } else {
            $this->nextPageUrl = null;
        }
    }

    /**
     * @return string
     */
    public function send($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_REFERER, 'http://localhost');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11");
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $content = utf8_decode(curl_exec($curl));
        curl_close($curl);
        return $content;
    }

    /**
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }
}