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
$this->params['breadcrumbs']['doctor'] = ArrayHelper::map($doctores, 'RUT', 'NOMBRE');

$posi = strrpos(get_class($model), "\\");
$nombreModelLow = strtolower(substr(get_class($model), $posi + 1));
$nombreModel = substr(get_class($model), $posi + 1);
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
                    <div class="col-md-2">
                        <div class="form-group">
                             <?= 
                                $form->field($model, 'fecha')->widget(DatePicker::className(),[
                                        'value' => date('d/m/Y'),
                                        'language' => 'es',
                                        'type' =>  DatePicker::TYPE_INPUT,
                                        'pickerButton' => [
                                            'icon'=>'ok',
                                        ],
                                        'options' => ['placeholder' => 'ELEGIR'],
                                        'pluginOptions' => [
                                                'format' => 'dd/mm/yyyy',
                                                'todayHighlight' => true
                                        ]
                                ])->label("DÍA:", ['class' => 'label label-default']);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <?= 
                               $form->field($model, 'hora')->widget(MaskedInput::className(),[
                                    'mask' => '##:##',
                                ])->label("HORA:", ['class' => 'label label-default']);
                            ?>

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= $form->field($model, 'doctor')->widget(Select2::classname(), [
                                    'data' => $this->params['breadcrumbs']['doctor'],
                                    'language' => 'es',
                                    'options' => ['placeholder' => 'ELEGIR', "class" => "form-control select2", "style" => 'width: 100%;'],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ])->label("DOCTOR:", ['class' => 'label label-default']);
							?>
						</div>
                    </div>
                    <div class="col-md-5">
                        
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
            var doc = $("#<?=$nombreModelLow?>-doctor").val();
            var dia = $("#<?=$nombreModelLow?>-fecha").val();
            var hora = $("#<?=$nombreModelLow?>-hora").val();
            if(doc != "" && dia != "" && hora != ""){
                miArray = dia.split("/");
                dia = miArray[2].concat(miArray[1]).concat(miArray[0]);
                hora = hora.replace(":","");
                var url = "<?php echo Yii::$app->request->baseUrl . '/index.php?r=operativos/reporte-operativo&doc='?>" + doc +  "&dia=" + dia + "&hora=" + hora;
                $("#reporte").attr("src",url);
                $("#reporte").show();
            }else{
                $("#modTitulo").html("Validación");
                $("#modBody").html("Se debe ingresar todos los valores");
                $("#myModal").removeClass();
                $("#myModal").addClass("modal modal-danger fade");
                $("#myModal").modal();
            }
            
        });
        var d = new Date();
        var dia = (d.getDate() < 10) ? "0".concat(d.getDate()) : d.getDate();
        var mes = (d.getMonth() < 9) ? "0".concat(d.getMonth()+ 1) : (d.getMonth()+ 1);
        var ano = d.getFullYear()
        var fecha = dia + '/' + mes+ '/' + ano;
        $("#<?= $nombreModelLow ?>-fecha").val(fecha);
    }
</script>

