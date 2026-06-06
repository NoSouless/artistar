<?php


namespace Source\Core;

use PDO;
use Exception;
use League\Plates\Engine;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Source\Model\Helpers\BuildLayout;
use Source\Model\Helpers\Translator;

class Core {
    protected $view;
    protected $nucleo;
    protected $router;
    protected $homenPath = 'artistar';
    protected $logado = false;
    protected $user = null;
    protected $NoSQL = NULL; 
    protected $SQL = NULL;
    protected $permissions = [];
    protected $isRedirect = false;
    protected $redirect = null;
    protected $language = 'pt-br';
    protected $layout = null;
    protected $translator = null;

    public function __construct($router = ROOT){
        global $db;
        $this->router=$router;
        $this->view = new Engine(dirname(__DIR__,1)."/Theme");
        $this->nucleo = $this->view;
        $this->SQL = $db;
        
        $this->verificaLogado();

        $this->defineRedirect();
        if ($this->getLogado() && $this->isRedirect()) {
            header("Location: " . $this->extractRedirect());
            exit;
        }

        $this->setLanguage(isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['artistar']['language']) ? $_SESSION['artistar']['language'] : 'pt-br'));
        $this->setTranslator(new Translator($this->getLanguage()));

        $this->setLayout(new BuildLayout($this->view));
        $this->getLayout()->setLang($this->getLanguage());
        $this->getLayout()->setTranslator((new Translator($this->getLanguage()))->loadTranslation('core'));
        $this->getLayout()->setDescription($this->getLayout()->getTranslator()->translate('Gerencie seu estoque de forma eficiente e prática durante eventos com o Artistar. Nossa plataforma facilita o controle de vendas, produtos e relatórios, proporcionando uma experiência otimizada para artistas e vendedores. Simplifique sua gestão e maximize seus lucros com nossas ferramentas intuitivas.'));

        $this->view->addData([
            "router"=> $this->router,
            "logado" => $this->getLogado(),
            "redirect" => $this->getRedirect(),
        ]);
    }

    public function setUserLogonStatus($user) {
        $_SESSION['artistar']['logon'] = $user;
    }

    public function getUserLogonStatus() {
        return isset($_SESSION['artistar']['logon']) ? $_SESSION['artistar']['logon'] : null;
    }

    public function unsetUserLogonStatus() {
        unset($_SESSION['artistar']['logon']);
    }

    public function setLanguage($language) {
        $this->language = $language;
        $_SESSION['artistar']['language'] = $language;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function verificaLogado(){
        $this->setLogado(false);
        if(!empty($this->getUserLogonStatus())) {
            $userStatement = $this->SQL->prepare('
                SELECT
                    usu.usuario_id id,
                    usu.usuario_nome nome,
                    usu.usuario_nome_completo nome_completo,
                    usu.usuario_email email,
                    usu.usuario_email_validado email_validado,
                    usu.usuario_envio_validacao envio_validacao,
                    loj.loja_id loja_id
                FROM
                    usuarios usu
                LEFT JOIN
                    lojas loj ON usu.usuario_id = loj.loja_proprietario
                WHERE
                    usu.usuario_id = :id
            ');
            $userId = $this->getUserLogonStatus();
            $userStatement->bindParam(':id', $userId, PDO::PARAM_INT);
            $userStatement->execute();
            $result = $userStatement->fetch();
            $result['foto_perfil'] = url('assets/image/favicon.png');
            if($result) {
                $this->setLogado(true);
                $this->setUser($result);
                $this->setPermissions([
                    'prototype' => false,
                    'admin' => ($result['id'] == 3)
                ]);
            } else {
                $this->unsetUserLogonStatus();
            }
        }
    }

    public function setLogado ($logado) {
        $this->logado = $logado;
    }

    public function getLogado () {
        return $this->logado;
    }

    public function setUser ($user) {
        $this->user = $user;
        $_SESSION['artistar']['user'] = $user;
    }

    public function getUser () {
        return $this->user;
    }

    public function setPermissions($permissions = []) {
        $this->permissions = $permissions; 
        $_SESSION['artistar']['permissions'] = $permissions;
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function defineRedirect() {
        if (isset($_GET['r']) && !empty($_GET['r'])) {
            $this->isRedirect = true;
            $redirect = $_GET['r'];
        } else {
            $redirect = base64_encode(urlencode($_SERVER['REQUEST_URI']));
        }
        $this->redirect = $redirect;
    }

    public function isRedirect() {
        return $this->isRedirect;
    }

    public function getRedirect() {
        return $this->redirect;
    }

    public function extractRedirect() {
        return urldecode(base64_decode($this->getRedirect()));
    }

    public function validaAcesso($redirectToValidation = true) {
        if(!$this->getLogado()){
            header("Location: /login?r=" . $this->getRedirect());
            exit;
        } 
        if($redirectToValidation) $this->checkIfEmailIsValidated();
    }

    public function checkIfEmailIsValidated() {
        if (!$this->getUser()['email_validado']) {
            header("Location: /register/validate-email?r=" . $this->getRedirect());
            exit;
        } 
    }

    public function getEstados() {
        return [
            ['id' => 1, 'name' => 'Acre', 'uf' => 'AC'],
            ['id' => 2, 'name' => 'Alagoas', 'uf' => 'AL'],
            ['id' => 3, 'name' => 'Amapá', 'uf' => 'AP'],
            ['id' => 4, 'name' => 'Amazonas', 'uf' => 'AM'],
            ['id' => 5, 'name' => 'Bahia', 'uf' => 'BA'],
            ['id' => 6, 'name' => 'Ceará', 'uf' => 'CE'],
            ['id' => 7, 'name' => 'Distrito Federal', 'uf' => 'DF'],
            ['id' => 8, 'name' => 'Espírito Santo', 'uf' => 'ES'],
            ['id' => 9, 'name' => 'Goiás', 'uf' => 'GO'],
            ['id' => 10, 'name' => 'Maranhão', 'uf' => 'MA'],
            ['id' => 11, 'name' => 'Mato Grosso', 'uf' => 'MT'],
            ['id' => 12, 'name' => 'Mato Grosso do Sul', 'uf' => 'MS'],
            ['id' => 13, 'name' => 'Minas Gerais', 'uf' => 'MG'],
            ['id' => 14, 'name' => 'Pará', 'uf' => 'PA'],
            ['id' => 15, 'name' => 'Paraíba', 'uf' => 'PB'],
            ['id' => 16, 'name' => 'Paraná', 'uf' => 'PR'],
            ['id' => 17, 'name' => 'Pernambuco', 'uf' => 'PE'],
            ['id' => 18, 'name' => 'Piauí', 'uf' => 'PI'],
            ['id' => 19, 'name' => 'Rio de Janeiro', 'uf' => 'RJ'],
            ['id' => 20, 'name' => 'Rio Grande do Norte', 'uf' => 'RN'],
            ['id' => 21, 'name' => 'Rio Grande do Sul', 'uf' => 'RS'],
            ['id' => 22, 'name' => 'Rondônia', 'uf' => 'RO'],
            ['id' => 23, 'name' => 'Roraima', 'uf' => 'RR'],
            ['id' => 24, 'name' => 'Santa Catarina', 'uf' => 'SC'],
            ['id' => 25, 'name' => 'São Paulo', 'uf' => 'SP'],
            ['id' => 26, 'name' => 'Sergipe', 'uf' => 'SE'],
            ['id' => 27, 'name' => 'Tocantins', 'uf' => 'TO'],
        ];
    }

    public function renderApiResponse($code, $message, $data = null) {
        $result = [];
        $result['code'] = $code;
        $result['message'] = $message;
        if (!empty($data)) $result['data'] = $data;
        return $this->view->render("apiResponse", [
            'result' => $result 
        ]);
    }

    public function moveFile($source, $destination) {
        if (!file_exists($source)) {
            return false;
        }
        $root = dirname(__DIR__, 2) . "/datafiles/";
        $fullDestination = $root . $destination;
        if (!file_exists(dirname($fullDestination))) {
            mkdir(dirname($fullDestination), 0777, true);
        }
        if (move_uploaded_file($source, $fullDestination)) {
            return "/datafiles/" . $destination;
        } else {
            return false;
        }
    }

    public function copyFile($source, $destination) {
        if (!file_exists(dirname(__DIR__, 2) . $source)) return false;
        $root = dirname(__DIR__, 2) . "/datafiles/";
        $fullDestination = $root . $destination;
        if (!file_exists(dirname($fullDestination))) {
            mkdir(dirname($fullDestination), 0777, true);
        }
        if (copy(dirname(__DIR__, 2) . $source, $fullDestination)) {
            return "/datafiles/" . $destination;
        } else {
            return false;
        }
    }

    public function deleteFile($path) {
        $fullPath = dirname(__DIR__, 2) . "/datafiles/" . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
            if (empty(glob(dirname($fullPath) . '/*'))) rmdir(dirname($fullPath));
            return true;
        }
        return false;
    }

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    public function getLayout() {
        return $this->layout;
    }

    public function setTranslator($translator) {
        $this->translator = $translator;
    }

    public function getTranslator() {
        return $this->translator;
    }

    public function addLayout($title = null) {
        $this->getLayout()->setTitle($title ? $title . ' - Artistar' : 'Artistar');
        $this->view->addData([
            "layout" => $this->getLayout()
        ]);
    }

    public function addTranslator($page) {
        $this->view->addData([
            "translator" => $this->getTranslator()->loadTranslation($page)
        ]);
    }
}