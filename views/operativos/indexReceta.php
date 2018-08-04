<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use app\models\entity\Perfiles;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BrcUsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $titulo;
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs']['rutaR'] = $rutaR;
$this->params['breadcrumbs']['pacientes'] = ArrayHelper::map($pacientes, 'RUT', 'NOMBRE');

?>
<?php
$form = ActiveForm::begin([
            'id' => 'login-form',
        ]);
?>
<div class="container-fluid">
            <div class="row">
                <div class="col-md-2">
                    <button type="button" id="btnBusProd" class="btn btn-block btn-sistema btn-flat">BUSCAR</button>
                </div>
                <div class="col-md-2">	
                    <?= Html::resetButton('LIMPIAR', ['class' => 'btn btn-block btn-sistema btn-flat', 'name' => 'limpiar-button']) ?>
                </div>
                <div class="col-md-8">	
                    &nbsp;
                </div>
            </div>
            <hr style="border: #FF6000 1px solid;">

            <div id="tomaHora">
                <div class="row">
  

                    <div class="col-md-12">
                        <div class="form-group">
                            <?=
            Select2::widget([
                'data' => $this->params['breadcrumbs']['pacientes'],
                'language' => 'es',
                'name' => 'pacienteBuscar',
                'options' => [ "class" => "form-control select2", "id" => "pacienteBuscar", "style" => 'width: 100%;'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
						</div>
                    </div>
                    
                </div>
            </div>
<?php ActiveForm::end(); ?>
    <hr style="border: #FF6000 1px solid;">
    <div class="row">
        <div class="col-md-12">
            <iframe id="reporte" width="100%" height="600px" src=""></iframe>

        </div>
    </div>
</div>
<script type="text/javascript">

    function initialComponets() {
        $("#reporte").hide();
        $("#btnBusProd").click(function(){
            $("#reporte").hide();
           var rut = $("#pacienteBuscar").val();
            if(rut != ""){

                var url = "<?php echo Yii::$app->request->baseUrl . '/index.php?r=operativos/reporte-receta&rut='?>" + rut;
                $("#reporte").attr("src",url);
                $("#reporte").show();
            }else{
                $("#modTitulo").html("Validación");
                $("#modBody").html("Debe seleccionar un paciente.");
                $("#myModal").removeClass();
                $("#myModal").addClass("modal modal-danger fade");
                $("#myModal").modal();
            }
            
        });
    }
</script>

