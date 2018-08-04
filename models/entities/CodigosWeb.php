<?php

namespace app\models\entities;

use Yii;

class CodigosWeb extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'brc_codigos_web';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TIPO', 'CODIGO', 'DESCRIPCION','IMG'], 'required'],
            [['TIPO', 'CODIGO'], 'string', 'max' => 6],
            [['DESCRIPCION'], 'string', 'max' => 50],
            [['IMG'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'TIPO' => 'TIPO',
            'CODIGO' => 'CÓDIGO',
            'DESCRIPCION' => 'DESCRIPCIÓN',
        ];
    }
}