<?php

namespace app\models\entities;

use Yii;

class InformeVenta {

    public function obtenerVentas($fecIni, $fecFin) {
        $venta = new \yii\db\Query;
        $venta->select([
                    "CONCAT('VENTA') as TIPO",
                    "brc_venta.FOLIO as FOLIO",
                    "brc_venta.FECHA_VENTA as FECHA",
                    "CONCAT('VENTA') as ESTADO",
                    "brc_venta.TOTAL as VALOR",
                ])
                ->from('brc_venta')
                ->where(['between', 'brc_venta.FECHA_VENTA',$fecIni, $fecFin])  
                ->orderBy(['brc_venta.FECHA_VENTA' => SORT_ASC,'brc_venta.FOLIO' => SORT_ASC]);
        return $venta;
    }
    
    public function obtenerAbonos($fecIni, $fecFin) {
        $abono = new \yii\db\Query;
        $abono->select([
                    "CONCAT('SALDO') as TIPO",
                    "brc_venta_abono.FOLIO as FOLIO",
                    "brc_venta_abono.FECHA_ABONO as FECHA",
                    "brc_codigos.DESCRIPCION as ESTADO",
                    "brc_venta_abono.VALOR as VALOR",
                ])
                ->from('brc_venta_abono')
                ->join("LEFT JOIN", "brc_codigos", "TIPO = 'ABONO' AND brc_venta_abono.TIPO_PAGO =brc_codigos.CODIGO")
                ->where(['between', 'brc_venta_abono.FECHA_ABONO',$fecIni, $fecFin])
                ->orderBy(['brc_venta_abono.FECHA_ABONO' => SORT_ASC,'brc_venta_abono.FOLIO' => SORT_ASC]);
        return $abono;
    }

    public function obtenerVentasAndAbonos($fecIni, $fecFin) {
        $venta = new \yii\db\Query;
        $abono = new \yii\db\Query;
        $venta->select([
                    "CONCAT('VENTA') as TIPO",
                    "brc_venta.FOLIO as FOLIO",
                    "brc_venta.FECHA_VENTA as FECHA",
                    "CONCAT('VENTA') as ESTADO",
                    "brc_venta.TOTAL as VALOR",
                ])
                ->from('brc_venta')
                ->where(['between', 'brc_venta.FECHA_VENTA',$fecIni, $fecFin]);
        
        $abono->select([
                    "CONCAT('SALDO') as TIPO",
                    "brc_venta_abono.FOLIO as FOLIO",
                    "brc_venta_abono.FECHA_ABONO as FECHA",
                    "brc_codigos.DESCRIPCION as ESTADO",
                    "brc_venta_abono.VALOR as VALOR",
                ])
                ->from('brc_venta_abono')
                ->join("LEFT JOIN", "brc_codigos", "TIPO = 'ABONO' AND brc_venta_abono.TIPO_PAGO =brc_codigos.CODIGO")
                ->where(['between', 'brc_venta_abono.FECHA_ABONO',$fecIni, $fecFin]);
        
        $query = (new \yii\db\Query())
        ->from(['dummy_name' => $venta->union($abono)])
        ->orderBy(['FECHA' => SORT_ASC, 'FOLIO' => SORT_ASC,'TIPO' => SORT_DESC]);
        
        return $query;
    }

}
