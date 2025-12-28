<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? 'Login', ENT_QUOTES) ?> – Engels811</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- FAVICON (CACHE-BUST) -->
    <link rel="icon"
          href="/favicon.ico?v=<?= htmlspecialchars(FaviconService::getVersion()) ?>"
          type="image/x-icon">

    <!-- GLOBAL -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- AUTH ONLY -->
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>

<body class="auth-body">
<main class="auth-main">
