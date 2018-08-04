<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>



<!DOCTYPE html>
<html lang=&quot;es&quot;>
     <head>
		<meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=utf-8&quot; />
		<meta name=&quot;description&quot; content=&quot;Esta Tienda está desarrollada con PrestaShop&quot; />
        <style>
            ::-moz-selection {background: #b3d4fc; text-shadow: none;}
            ::selection {background: #b3d4fc; text-shadow: none;}
            html {padding: 30px 10px; font-size: 16px; line-height: 1.4; color: #737373; background: #f0f0f0;
                -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;}
            html,
            input {font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;}
            body {max-width:50%; _width: 50%; padding: 0; border: 1px solid #b3b3b3;
                border-radius: 4px;margin: 0 auto; box-shadow: 0 1px 10px #a7a7a7, inset 0 1px 0 #fff;
                background: #fcfcfc;
                -webkit-box-shadow: 15px 15px 43px 0px rgba(0,0,0,0.61);
                -moz-box-shadow: 15px 15px 43px 0px rgba(0,0,0,0.61);
                box-shadow: 15px 15px 43px 0px rgba(0,0,0,0.61);
            }
            h1 {margin: 0 10px; font-size: 50px; }
            a {color:red; text-decoration:red underline solid}
            a:hover {color:red; text-decoration:red underline solid}
            h1 span {color: #bbb;}
            h2 {color: #fcfcfc;font-size: 20px;}
            h2 span {color: #fcfcfc;font-size: 30px;}
            h3 {margin: 1.5em 0 0.5em;}
            p {margin: 10px 10px;font-size: 15px;}
            ul {padding: 0 0 0 40px;margin: 1em 0;}
            .container {width: 100%;max-width: 100%;_width: 100%; padding: 0; margin: 0}
            input::-moz-focus-inner {padding: 0;border: 0;}
            header{
                    border-bottom-color: #f4f4f4;
                    background-color: red;
                    color: white;
                    padding: 15px;
                    border-bottom: 1px solid #e5e5e5;
            }
           
        </style>
    </head>
    <body class="modal-content">
        <div class="container ">
            <header>
                <h2><span>ERROR N°: <?=$name?>.</span></h2>
                <h2>Message: <?= $message ?></h2>
                
            </header>
            <section>
                
                <p>Ha ocurrido un error en algún momento del procesamiento de datos.  
                   Se aconseja que se comunique con el administrador del sistema o presionar el link 
                   para retorno a la pagina de inicio. <a href="<?= Yii::$app->request->baseUrl?>">Ir a Sistema</a>
                </p>
            </section>
            <footer>
                
            </footer>
        </div>
        
    </body>
</html>

