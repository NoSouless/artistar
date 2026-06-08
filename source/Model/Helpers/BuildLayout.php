<?php

namespace Source\Model\Helpers;

class buildLayout {

    protected $view;
    protected $title;
    protected $lang;
    protected $description;
    protected $favicon;
    protected $url;
    protected $header;
    protected $footer;
    protected $translator;

    public function __construct($view = null) {
        $this->setView($view);
        $this->setTitle('Artistar');
        $this->setLang('pt-br');
        $this->setDescription('Gerencie seu estoque de forma eficiente e prática durante eventos com o Artistar. Nossa plataforma facilita o controle de vendas, produtos e relatórios, proporcionando uma experiência otimizada para artistas e vendedores. Simplifique sua gestão e maximize seus lucros com nossas ferramentas intuitivas.');
        $this->setFavicon(url("assets/image/favicon.png"));
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $this->setUrl($protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $this->setHeader('');
        $this->setFooter('');
    }

    public function setView($view) {
        $this->view = $view;
    }

    public function getView() {
        return $this->view;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setLang($lang) {
        $this->lang = $lang;
    }

    public function getLang() {
        return $this->lang;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setFavicon($favicon) {
        $this->favicon = $favicon;
    }

    public function getFavicon() {
        return $this->favicon;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setHeader($header) {
        $this->header = $header;
    }

    public function getHeader() {
        return $this->header;
    }

    public function buildHeader() {
        $arr = $_GET;
        unset($arr['lang']);
        unset($arr['route']);
        $urlWithoutLang = '?'. http_build_query($arr);
        if (!empty($this->getHeader())) {
            return $this->getView()->render("fragments/" . $this->getHeader(), [
                'urlWithoutLang' => $urlWithoutLang,
                'activeLanguage' => $this->getLang(),
                "translator" => $this->getTranslator(),
                'languageOptions' => [
                    'pt-br' => [
                        'flag' => 'br.png',
                        'label' => 'Português'
                    ],
                    'en-us' => [
                        'flag' => 'us.png',
                        'label' => 'English'
                    ]
                ],
            ]);
        }
    }

    public function setFooter($footer) {
        return $this->footer = $footer;
    }

    public function getFooter() {
        return $this->footer;
    }

    public function buildFooter() {
        if (!empty($this->getFooter())) {
            return $this->getView()->render("fragments/" . $this->getFooter(), [
                "translator" => $this->getTranslator()
            ]);
        }
    }

    public function setTranslator($translator) {
        $this->translator = $translator;
    }

    public function getTranslator() {
        return $this->translator;
    }

}