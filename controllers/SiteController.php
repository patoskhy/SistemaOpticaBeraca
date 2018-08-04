<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\forms\LoginForm;
use app\models\forms\UploadForm;
use app\models\utilities\Utils;
use yii\helpers\VarDumper;
use yii\imagine\Image;
use Imagine\Gd;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use app\models\entities\Ventas;
use app\models\entities\VentasAbono;
use app\models\entities\VentasDetalle;
use app\models\entities\Compras;
use app\models\entities\ComprasDetalle;
use app\models\entities\Operativo;
use app\models\entities\OperativosDetalle;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex() {
        $id;
        if (empty($id)) {
            $id = 0;
        }

        $msg = "";
        if (Yii::$app->request->get()) {
            if (!empty($_GET['msg'])) {
                $msg = $_GET['msg'];
            }
        }
        //var_dump($msg);die();
        $model = new LoginForm();
        //si no es invitado
        if (!Yii::$app->user->isGuest) {
            $data['ventas'] = Ventas::pagIniVentas();
            $data['compras'] = Compras::pagIniCompras();
            $data['operativos'] = OperativosDetalle::pagIniOperativos();
            $data['abonos'] = VentasAbono::pagIniAbonos();
            $data['donaciones'] = Compras::pagIniDonaciones();
            $titulo = $GLOBALS["nombreSistema"];
            $perfiles = $model->getPerfil(Yii::$app->user->identity->username, $id);
            $this->view->params['menuLeft'] = Utils::getMenuLeft(explode("-", Yii::$app->user->id)[0]);
            $this->view->params['titlePage'] = strtoupper("panel principal");
            $this->layout = 'main';
            //var_dump($data);die();
            return $this->render('index', ["titulo" => $titulo,"data"=>$data,"msg" => $msg]);
        }

        //si envio post del form de usuario
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $data['ventas'] = Ventas::pagIniVentas();
            $data['compras'] = Compras::pagIniCompras();
            $data['operativos'] = OperativosDetalle::pagIniOperativos();
            $data['abonos'] = VentasAbono::pagIniAbonos();
            $data['donaciones'] = Compras::pagIniDonaciones();
            $titulo = $GLOBALS["nombreSistema"];
            $perfiles = $model->getPerfil($model->username, $id);
            $this->view->params['menuLeft'] = Utils::getMenuLeft(explode("-", Yii::$app->user->id)[0]);
            $this->view->params['titlePage'] = strtoupper("panel principal");
            $this->layout = 'main';
            //var_dump($data);die();
            return $this->render('index', ["titulo" => $titulo,"data"=>$data,"msg" => $msg]);
        }
        $titulo = $GLOBALS["nombreSistema"];
        $this->view->params['titlePage'] = strtoupper("INICIO DE SESIÓN");
        //Al login
        $this->layout = 'main-login';
        return $this->render('login', [
                    'model' => $model,
                    'titulo' => $titulo,
        ]);
    }

    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->actionIndex();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionEditarImagen() {
        if (empty($id)) {
            $id = 0;
        }
        $titulo = $GLOBALS["nombreSistema"];

        $msg = "";
        $model = new LoginForm();

        //si no es invitado
        if (!Yii::$app->user->isGuest) {
            $perfiles = $model->getPerfil(Yii::$app->user->identity->username, $id);
            $this->view->params['menuLeft'] = Utils::getMenuLeft(explode("-", Yii::$app->user->id)[0]);
            $this->view->params['titlePage'] = strtoupper("EDICIÓN DE IMÁGENES");
            $this->layout = 'main';
            return $this->render('indexEdicion', ["titulo" => $titulo,'descarga' => false,]);
        }

        //si envio post del form de usuario
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $titulo = $GLOBALS["nombreSistema"];
            $perfiles = $model->getPerfil($model->username, $id);
            $this->view->params['menuLeft'] = Utils::getMenuLeft(explode("-", Yii::$app->user->id)[0]);
            $this->view->params['titlePage'] = strtoupper("EDICIÓN DE IMÁGENES");
            $this->layout = 'main';
            return $this->render('indexEdicion', ["titulo" => $titulo,'descarga' => false,]);
        }
        //Al login
        $titulo = $GLOBALS["nombreSistema"];
        $this->view->params['titlePage'] = strtoupper("INICIO DE SESIÓN");
        //Al login
        $this->layout = 'main-login';
        return $this->render('login', [
                    'model' => $model,
                    'titulo' => $titulo,
                    
        ]);
    }

    public function actionFileUpload() {
        if (!Yii::$app->user->isGuest) {
            $tmp = $_FILES['file']['tmp_name'];
            //var_dump($_FILES['file']);die();
            $dir = "uploads/";
            $fileName = 'img.jpg';
            //unlink($dir . $fileName);
            Image::getImagine()->open($tmp)
                ->thumbnail(new Box(250, 150))
                ->save($dir . $fileName, ['quality' => 90]);
            return true;
        }
        return false;
        
    }

    public function actionFileDownload() {
        if (!Yii::$app->user->isGuest) {
            $dir = "uploads/";
            $fileName = 'img.jpg';
            $model = new LoginForm();

                if (!UploadForm::downloadFile($dir, $fileName, ["jpg"])) {
                    //unlink($dir . $fileName);
                    $titulo = $GLOBALS["nombreSistema"];
                    $this->view->params['menuLeft'] = Utils::getMenuLeft(explode("-", Yii::$app->user->id)[0]);
                    $this->view->params['titlePage'] = strtoupper("EDICIÓN DE IMÁGENES");
                    $this->layout = 'main';
                   // return $this->render('indexEdicion', ["titulo" => $titulo,'descarga' => true,]);
                }
        }
    }

}
