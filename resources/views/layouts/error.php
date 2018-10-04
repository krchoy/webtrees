<?php use Fisharebest\Webtrees\I18N; ?>
<!DOCTYPE html>
<html <?= I18N::htmlAttributes() ?>>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">

        <title><?= I18N::translate('This website is temporarily unavailable') ?></title>

        <link rel="icon" href="favicon.ico" type="image/x-icon">

        <style type="text/css">
            body {color: gray; background-color: white; font: 14px tahoma, arial, helvetica, sans-serif; padding:10px; }
            a {color: #81A9CB; font-weight: bold; text-decoration: none;}
            a:hover {text-decoration: underline;}
            h1 {color: #81A9CB; font-weight:normal; text-align:center;}
            li {line-height:2;}
            blockquote {color:red;}
            .content { border:1px solid gray; padding:15px; margin: 15px; border-radius:15px;}
            .good {color: green;}
        </style>
    </head>

    <body>
        <h1><?= I18N::translate('This website is temporarily unavailable') ?></h1>

        <?= $content ?>
    </body>
</html>
