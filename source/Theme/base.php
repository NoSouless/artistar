<!DOCTYPE html>
<html lang="<?= $layout->getLang() ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?= $layout->getTitle() ?></title>
        <meta name="description" content="<?= $layout->getDescription() ?>">
        <meta property="og:title" content="<?= $layout->getTitle() ?>">
        <meta property="og:description" content="<?= $layout->getDescription() ?>">
        <meta property="og:image" content="<?= $layout->getFavicon() ?>">
        <meta property="og:url" content="<?= $layout->getUrl() ?>">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="<?= $layout->getTitle() ?>">
        <meta name="twitter:description" content="<?= $layout->getDescription() ?>">
        <meta name="twitter:image" content="<?= $layout->getFavicon() ?>">
        <link rel="shortcut icon" href="<?= $layout->getFavicon() ?>" />
        <link rel="stylesheet" href="<?= url("assets/vendors/bootstrap-5.3.3/css/bootstrap.min.css") ?>">
        <link rel="preload" href="<?= url("assets/vendors/fontawesome-6.6.0/css/all.min.css") ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <link rel="preload" rel="stylesheet" href="<?= url("assets/css/artistar.css?t=" . time()) ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <script src="https://code.iconify.design/2/2.2.1/iconify.min.js" defer></script>
        <?= $this->section("css") ?>
    </head>
    <body>
        <?= $layout->buildHeader() ?>
        <?= $this->section("conteudo") ?>
        <?= $layout->buildFooter() ?>
    </body>
    <script src="<?= url("assets/vendors/bootstrap-5.3.3/js/bootstrap.bundle.min.js") ?>" defer></script>
    <script src="<?= url("assets/js/jquery-3.6.0.js") ?>"></script>
    <script src="<?= url("assets/js/artistar.js?t=" . time()) ?>"></script>
    <?= $this->section("js") ?>
</html>