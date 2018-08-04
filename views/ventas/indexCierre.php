<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use app\models\entity\Perfiles;
use kartik\select2\Select2;
use kartik\date\DatePicker;

$this->title = $titulo;
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs']['rutaR'] = $rutaR;
$this->params['breadcrumbs']['tipo'] = ArrayHelper::map($tipo, 'CODIGO', 'DESCRIPCION');

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
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2">
<?= Html::submitButton('GUARDAR', ['class' => 'btn btn-block btn-sistema btn-flat', 'name' => 'guardar-button']) ?>
                </div>
                <div class="col-md-2">	
<?= Html::resetButton('LIMPIAR', ['class' => 'btn btn-block btn-sistema btn-flat', 'name' => 'limpiar-button']) ?>
                </div>
                <div class="col-md-8">	
                    &nbsp;
                </div>
            </div>
            <hr style="border: #FF6000 1px solid;">
            <div class="row">
                <div  class="col-md-6">
                    <div class="form-group">
                         <?=
                        $form->field($model, 'tipo')->widget(Select2::classname(), [
                            'data' => $this->params['breadcrumbs']['tipo'],
                            'language' => 'es',
                            'options' => ["class" => "form-control select2", "style" => 'width: 100%;'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label("TIPO:", ['class' => 'label label-default']); 
                        ?>
                    </div>
                </div>
                <div  class="col-md-6">
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
                                ])->label("FECHA:", ['class' => 'label label-default']);
                            ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">

    function initialComponets() {
        var d = new Date();
        var dia = (d.getDate() < 10) ? "0".concat(d.getDate()) : d.getDate();
        var mes = (d.getMonth() < 9) ? "0".concat(d.getMonth()+ 1) : (d.getMonth()+ 1);
        var ano = d.getFullYear()
        var fecha = dia + '/' + mes+ '/' + ano;
        $("#<?= $nombreModelLow ?>-fecha").val(fecha);
        var msg = "<?=$msg?>"
        if(msg != ""){
            
            $("#modTitulo").html("Validación");
            $("#modBody").html("<?=$msg?>");
            $("#myModal").removeClass();
            if(msg == "OK"){
                $("#modBody").html("Estado cambiado con éxito");
                $("#myModal").addClass("modal modal-success fade");
            }else{
                $("#myModal").addClass("modal modal-danger fade");
            }
            $("#myModal").modal();
        }
    }
    
    
</script>