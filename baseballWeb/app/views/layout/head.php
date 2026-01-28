<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $pageTitle ?? '2D Baseball'; ?></title>
    <meta name="description" content="<?= $pageDescription ?? 'Description is missing'; ?>">
    <meta name="author" content="Filip Kučera">

    <link rel="canonical" href="https://xeon.spskladno.cz/~kuceraf/2DBaseball/BaseballWeb/<?php echo $_SERVER['REQUEST_URI']; ?>">

    <link rel="icon" type="image/x-icon" href="<?= $baseUrl ?>public/images/favicon/favicon.ico?v=<?= time(); ?>">

    <link rel="stylesheet" href="<?= $baseUrl ?>public/css/pageshared.css?v=<?= time(); ?>">
        <link rel="stylesheet" href="<?= $baseUrl ?>public/css/fonts.css?v=<?= time(); ?>">
    <?php if (!empty($pageCSS)) echo $pageCSS; ?>

    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="rgb(17, 17, 17)">

    <link href="<?= $baseUrl ?>app/lib/boxicons/css/boxicons.min.css" rel="stylesheet">

    <script src="<?= $baseUrl ?>app/lib/emailjs-com/dist/email.min.js"></script>
</head>


