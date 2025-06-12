<?php
require_once __DIR__ . "/../config/config.php";
$gelisim = $GLOBALS['app_config']['debug'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <?php
    if ($gelisim) {
        if (isset($error)) {

            ?>

            <p class="alert"><?= $error ?></p>
            <?php
        }

    } else {
        ?>
        <h1> 404 NOT FOUND </h1>

        <?php
    }
    ?>


    <style>
        body {
            display: flex;
            width: 100%;
            height: auto;
            justify-content: center;
            align-items: center;

        }

        .alert {
            border: 1px solid black;
            text-align: center;
            background-color: bisque;
            padding: 8px 16px;
        }
    </style>
</body>

</html>