<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>PGUnit results</title>
        <style>
            th {
                border: solid 1px activeborder;
                font-weight: bold;
                padding: 4px;
            }
            td {
                border: solid 1px activeborder;                
            }
            /*
                word-wrap: normal;
            }
            .alert.alert-success{
            table{
                width: 95%;
                margin-left: auto;
                margin-right: auto;
            }
            */
        </style>
    </head>
    <body>
        <h1>PGUnit test results</h1>
        <h2>
            <?php
            echo $group
            ?>
        </h2>
        <?php
        echo $view_data;
        ?>
    </body>
</html>
