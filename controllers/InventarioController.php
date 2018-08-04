<?php

//require_once __DIR__ . '../composer/autoload_real.php';

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\entities\Codigos;
use app\models\entities\Proveedor;
use app\models\entities\Producto;
use app\models\entities\ProveedorProducto;
use app\models\forms\CompraProductoForm;
use app\models\forms\LoginForm;
use app\models\entities\Inventario;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use JasperPHP\JasperPHP;
use app\models\utilities\Utils;
use yii\data\ActiveDataProvider;

/**
 * BrcUsuariosController implements the CRUD actions for BrcUsuarios model.
 */
class InventarioController extends Controller {

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

    public function actionIndexInventario($id, $t) {
        if (!Yii::$app->user->isGuest && Utils::validateIfUser($id)) {
            if (empty($id)) {
                $id = 0;
            }
            $titulo = $GLOBALS["nombreSistema"];
            $model = new CompraProductoForm;
            $rutaR = "&rt=" . $id . "&t=" . $t;
            $folio = "000000000000";
            $prod = "";
            if (Yii::$app->request->get()) {
                if (!empty($_GET['producto'])) {
                    $prod = $_GET['producto'];
                }
            }

            if ($model->load(Yii::$app->request->post())) {
                return $this->redirect("index.php");
            } else {
                $_SESSION["action"] = "envio";
                $query = "";
                if ($prod == "TODOS") {
                    $query = Inventario::obtenerInventario();
                } else {
                    $query = Inventario::obtenerInventarioPorProducto($prod);
                }

                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'pagesize' => 7,
                    ],
                ]);

                $producto = Producto::obtenerTodosProductos();
                $this->view->params['titlePage'] = strtoupper($t);
                $this->view->params['menuLeft'] = Utils::getMenuLeft(explode("-", Yii::$app->user->id)[0]);
                $this->layout = 'main';
                return $this->render('indexInventario', [
                            'model' => $model,
                            'titulo' => $titulo,
                            'rutaR' => $rutaR,
                            'producto' => $producto,
                            'dataProvider' => $dataProvider,
                ]);
            }
        }
        return $this->redirect("index.php");
    }

}
