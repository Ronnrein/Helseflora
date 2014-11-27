<?php
require_once("Classes/Config.php");
use Classes\Config;
$url = str_replace(basename($_SERVER['PHP_SELF']), "", Config::getUrl());
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Examples</title>
        <link rel="stylesheet" type="text/css" href="https://rawgit.com/yesmeck/jquery-jsonview/master/dist/jquery.jsonview.css" />
        <style type="text/css">
            body{
                width: 100%;
            }
            div#left{
                float: left;
                width: 30%;
            }
            div#right{
                float: right;
                width: 70%;
            }
            div#clear{

            }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript"></script>
        <script src="https://rawgit.com/yesmeck/jquery-jsonview/master/dist/jquery.jsonview.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#left a").click(function(e){
                    e.preventDefault();
                    $.getJSON($(this).attr("href"), function(json){
                        $("#right").JSONView(json);
                    });
                });
            });
        </script>
    </head>
    <body>
        <div id="left">
            <ul>
                <li><a href="<?=$url?>?a=getAll&what=plants">Få alle planter</a></li>
                <li><a href="<?=$url?>?a=get&what=plant&id=5">Få en plante</a></li>
                <li><a href='<?=$url?>?a=getMultiple&what=plants&ids={"22": 2, "30": 5, "38": 2}'>Få flere planter</a></li>
                <li><a href="<?=$url?>?a=getAllCategoriesPlants">Få alle kategorier og tilhørende planter</a></li>
                <li><a href="<?=$url?>?a=get&what=user&id=1">Få bruker</a></li>

            </ul>
        </div>
        <div id="right"></div>
    </body>
</html>