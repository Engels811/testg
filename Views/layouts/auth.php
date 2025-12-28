<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? 'Login') ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- NUR AUTH CSS -->
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>

<body class="auth-body">

    <?= $content ?>

</body>
</html>
