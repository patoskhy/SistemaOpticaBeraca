<?php

namespace app\controllers;

/* CORE */

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\imagine\Image;
use Imagine\Gd;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;

/* ENTITIES */
use app\models\entities\Producto;
use app\models\entities\Codigos;
use app\models\entities\CodigosWeb;
use app\models\entities\ProductoWeb;

/* FORM */
use app\models\forms\CodigosWebForm;
use app\models\forms\ProductoWebForm;

/* UTILIDADES */
use app\models\utilities\Utils;

class SitiowebController extends Controller {

    /**
     * @inheritdoc
     */
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

    public function actionIndexCodigos($id, $t) {
        if (!Yii::$app->user->isGuest && Utils::validateIfUser($id)) {
            if (empty($id)) {
                $id = 0;
            }
            $titulo = $GLOBALS["nombreSistema"];
            $rutaR = "&rt=" . $id . "&t=" . $t;
            $pref = "uploads/codWeb/";
            $model = new CodigosWebForm;

            if ($model->load(Yii::$app->request->post())) {
                //var_dump($model);die();
                $cod = $model->codigo;
                $res = "";
                if (empty($cod) || is_null($cod)) {
                    $res = CodigosWeb::find()->where("TIPO='" . $model->tipo . "'")->max('CODIGO');
                    //var_dump($res);die();
                    if (is_null($res)) {
                        $cod = "000001";
                    } else {
                        $tmpH = $res + 1;
                        $cod = str_pad($tmpH, 6, "0", STR_PAD_LEFT);
                    }
                }
                //var_dump($cod);die();
                $model->img = UploadedFile::getInstance($model, "img");
                $imageName1 = $model->tipo . "-" . $cod . "." . $model->img->extension;
                $model->img->saveAs($pref . $imageName1, true);
                $pathImg1 = Yii::$app->basePath . "/web/uploads/codWeb/" . $imageName1;
                $gestor1 = fopen($pathImg1, "rb");
                $base64image1 = base64_encode(fread($gestor1, filesize($pathImg1)));
                fclose($gestor1);

                $existe = CodigosWeb::find()->where("TIPO='" . $model->tipo . "' AND CODIGO='" . $cod . "'")->all();

                if (empty($existe)) {
                    $codigo = new CodigosWeb;

                    $codigo->TIPO = $model->tipo;
                    $codigo->CODIGO = $cod;
                    $codigo->DESCRIPCION = $model->descripcion;
                    $codigo->IMG = "/uploads/codWeb/" . $imageName1;
                    $codigo->insert();
                    VAR_DUMP($codigo->getErrors());die();
                } else {
                    if (CodigosWeb::deleteAll("TIPO='" . $model->tipo . "' AND CODIGO='" . $cod . "'")) {
                        $codigo = new CodigosWeb;
                        $codigo->TIPO = $model->tipo;
                        $codigo->CODIGO = $cod;
                        $codigo->DESCRIPCION = $model->descripcion;
                        $codigo->IMG = "/uploads/codWeb/" . $imageName1;
                        $codigo->insert();
                    }
                }
                /*
                // SE ELIMINO POR NO SER SERVIDOR LOCAS Y PUBLIAR EN LA NUBE
                //programar el ingreso al web service
                $ip = "www.google.com";
                if (Utils::GetPing($ip) == 'perdidos),') {
                    
                } else if (Utils::GetPing($ip) == '0ms') {
                    
                } else {
                    $soapClient = Yii::$app->siteApi;
                    $res = $soapClient->InsertarCodWeb(
                            $model->tipo, $cod, $model->descripcion, $base64image1
                    );
                    //var_dump($res);die();
                }
                */
            }
            $codi = "TODOS";
            if (Yii::$app->request->get()) {
                if (!empty($_GET['tipBus'])) {
                    $codi = $_GET['tipBus'];
                }
            }
            $query = CodigosWeb::find();
            if ($codi != "TODOS") {
                $query = CodigosWeb::find()->where("TIPO='" . $codi . "'");
            }

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pagesize' => 7,
                ],
            ]);
            $utils = new Utils;
            $sql = "SELECT DESCRIPCION FROM brc_codigos WHERE TIPO = 'WEB_CA'";
            $tipo = $utils->ejecutaQuery($sql);
            $this->view->params['titlePage'] = strtoupper($t);
            $this->view->params['menuLeft'] = Utils::getMenuLeft(explode("-", Yii::$app->user->id)[0]);
            $this->layout = 'main';
            return $this->render('indexCodigosWeb', [
                        'titulo' => $titulo,
                        'rutaR' => $rutaR,
                        'model' => $model,
                        'tipo' => $tipo,
                        'dataProvider' => $dataProvider,
            ]);
        }
        return $this->redirect("index.php");
    }

    public function actionIndexProductos($id, $t) {
        if (!Yii::$app->user->isGuest && Utils::validateIfUser($id)) {
            if (empty($id)) {
                $id = 0;
            }
            $titulo = $GLOBALS["nombreSistema"];
            $rutaR = "&rt=" . $id . "&t=" . $t;

            $model = new ProductoWebForm();
            $color = CodigosWeb::find()->where("TIPO = 'COLOR'")->all();
            $forma = CodigosWeb::find()->where("TIPO = 'FORMA'")->all();
            $material = CodigosWeb::find()->where("TIPO = 'MATERIAL'")->all();
            $marca = CodigosWeb::find()->where("TIPO = 'MARCA'")->all();
            $producto = Producto::find()->where("(SUBSTRING(ID_HIJO,1,1) ='1' OR SUBSTRING(ID_HIJO,1,1) ='5') AND LENGTH(ID_HIJO) = 11")->all();
            $vigencia = Codigos::find()->where("TIPO = 'EST_BO'")->all();
            $tipo = CodigosWeb::find()->where("TIPO = 'TIPO'")->all();
            
            $proBus = "TODOS";
            if (Yii::$app->request->get()) {
                if (!empty($_GET['proBus'])) {
                    $proBus = $_GET['proBus'];
                }
            }
            $query = ProductoWeb::find();
            if ($proBus != "TODOS") {
                $query = ProductoWeb::find()->where("CODIGO='" . $proBus . "'");
            }

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pagesize' => 7,
                ],
            ]);
            
            if ($model->load(Yii::$app->request->post())) {
                /* GUARDAMOS LAS FOTOS EN UPLOADS */
                $pref = "uploads/";
                $model->foto1 = UploadedFile::getInstance($model, "foto1");
                $model->foto2 = UploadedFile::getInstance($model, "foto2");
                $imageName1 = "";
                $imageName2 = "";
                if($model->tipo == "000002"){
                    $imageName1 = "foto1-" . $model->codigo . "-". $model->modelo . "." . $model->foto1->extension;
                    $imageName2 = "foto2-" . $model->codigo . "-". $model->modelo . "." . $model->foto2->extension;
                }else{
                    $imageName1 = "foto1-" . $model->codigo . "." . $model->foto1->extension;
                    $imageName2 = "foto2-" . $model->codigo . "." . $model->foto2->extension;
                }
                
                //$model->foto1->saveAs($pref . $imageName1, true);
                //$model->foto2->saveAs($pref . $imageName2, true);
                //var_dump($model->foto1->tempName);die();
                Image::getImagine()->open($model->foto1->tempName)
                ->thumbnail(new Box(250, 250))
                ->save($pref . $imageName1, ['quality' => 90]);

                Image::getImagine()->open($model->foto2->tempName)
                ->thumbnail(new Box(250, 250))
                ->save($pref . $imageName2, ['quality' => 90]);


                $currenProd = Producto::obtenerProductosByCodigoBarraWeb($model->codigo);
                $model->descripcion = $currenProd[0]["DESCRIPCION"];
                $model->valor = $currenProd[0]["VALOR_VENTA"];

                /* GUARDAMOS LOS DATOS */

                $productoWeb = new ProductoWeb;
                $productoWeb->CODIGO = $model->codigo;
                $productoWeb->DESCRIPCION = $model->descripcion;
                $productoWeb->VIGENCIA = $model->vigencia;
                $productoWeb->VALOR = $model->valor;
                $productoWeb->COD_TIPO = $model->tipo;
                $productoWeb->COD_MARCA = $model->marca;
                $productoWeb->MODELO = $model->modelo;
                $productoWeb->COD_MATERIAL = $model->material;
                $productoWeb->COD_COLOR = $model->color;
                $productoWeb->COD_FORMA = $model->forma;

                $pathImg1 = Yii::$app->basePath . "/web/uploads/" . $imageName1;
                $gestor1 = fopen($pathImg1, "rb");
                $base64image1 = base64_encode(fread($gestor1, filesize($pathImg1)));
                fclose($gestor1);
                $productoWeb->FOTO1 = $base64image1;

                $pathImg2 = Yii::$app->basePath . "/web/uploads/" . $imageName2;
                $gestor2 = fopen($pathImg2, "rb");
                $base64image2 = base64_encode(fread($gestor2, filesize($pathImg2)));
                fclose($gestor2);
                $productoWeb->FOTO2 = $base64image2;

                 /*
                // SE ELIMINO POR NO SER SERVIDOR LOCAL Y PUBLICAR EN LA NUBE
                $ip = "www.google.com";
                if (Utils::GetPing($ip) == 'perdidos),') {
                    
                } else if (Utils::GetPing($ip) == '0ms') {
                    
                } else {
                    $client = Yii::$app->siteApi;
                    $res = $client->InsertarItem(
                            $model->codigo, $model->descripcion, $model->vigencia, $model->valor, $model->marca, $model->modelo, $model->material, $model->color, $model->forma, $base64image1, $base64image2, $model->tipo
                    );
                }
                */
                $pw = ProductoWeb::find()->where("CODIGO='" . $model->codigo . "' AND COD_TIPO = '".$model->tipo."'  AND COD_MARCA = '".$model->marca."'  AND COD_MATERIAL = '".$model->material."' AND COD_COLOR = '".$model->color."' AND COD_FORMA = '".$model->forma."' AND MODELO = '".$model->modelo."'")->one();
                //var_dump($pw->createCommand()->sql);die();
                if (is_null($pw)) {

                    if ($productoWeb->insert()) {
                        
                    } else {
                        //var_dump($productoWeb->getErrors());
                    }
                } else {
                    $pw->delete();
                    if ($productoWeb->insert()) {
                        //var_dump("paso");die();
                    } else {
                        //var_dump($productoWeb->getErrors()); 
                    }
                }

                $model = new ProductoWebForm();
                $this->view->params['titlePage'] = strtoupper($t);
                $this->view->params['menuLeft'] = Utils::getMenuLeft(explode("-", Yii::$app->user->id)[0]);
                $this->layout = 'main';
                return $this->render('indexProductoWeb', [
                            'titulo' => $titulo,
                            'rutaR' => $rutaR,
                            'model' => $model,
                            'color' => $color,
                            'forma' => $forma,
                            'material' => $material,
                            'marca' => $marca,
                            'producto' => $producto,
                            'vigencia' => $vigencia,
                            'tipo' => $tipo,
                            'enviado' => "SI",
                            'dataProvider' => $dataProvider,
                ]);
            } else {
                $this->view->params['titlePage'] = strtoupper($t);
                $this->view->params['menuLeft'] = Utils::getMenuLeft(explode("-", Yii::$app->user->id)[0]);
                $this->layout = 'main';
                return $this->render('indexProductoWeb', [
                            'titulo' => $titulo,
                            'rutaR' => $rutaR,
                            'model' => $model,
                            'color' => $color,
                            'forma' => $forma,
                            'material' => $material,
                            'marca' => $marca,
                            'producto' => $producto,
                            'vigencia' => $vigencia,
                            'tipo' => $tipo,
                            'enviado' => "NO",
                            'dataProvider' => $dataProvider,
                ]);
            }
        }
        return $this->redirect("index.php");
    }

}
