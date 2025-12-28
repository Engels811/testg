<?php
/** @var string|null $title */
http_response_code(500);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Serverfehler', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; background:#111; color:#eee; padding:40px">

    <h1>⚠️ Interner Serverfehler</h1>

    <p>
        Es ist ein unerwarteter Fehler aufgetreten.<br>
        Bitte versuche es später erneut.
    </p>

    <p style="opacity:.6">
        Fehlercode: 500
    </p>

</body>
</html>
