<?php
declare(strict_types=1);

class AuthController
{
    /* =========================================================
       LOGIN
    ========================================================= */
    public function login(): void
    {
        $error = null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $login    = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';
            $rememberDevice = !empty($_POST['remember_device']);

            if ($login === '' || $password === '') {
                $error = 'Bitte alle Felder ausfüllen.';
            } else {

                /* =========================================
                   USER + ROLLE LADEN (RBAC – FIXED)
                ========================================= */
                $sql = filter_var($login, FILTER_VALIDATE_EMAIL)
                    ? '
                        SELECT u.*,
                               r.id    AS role_id,
                               r.name  AS role_name,
                               r.level AS role_level
                        FROM users u
                        JOIN roles r ON r.name = u.role
                        WHERE u.email = ?
                        LIMIT 1
                      '
                    : '
                        SELECT u.*,
                               r.id    AS role_id,
                               r.name  AS role_name,
                               r.level AS role_level
                        FROM users u
                        JOIN roles r ON r.name = u.role
                        WHERE u.username = ?
                        LIMIT 1
                      ';

                $user = Database::fetch($sql, [$login]);

                if (!$user || !password_verify($password, $user['password'])) {
                    $error = 'Login fehlgeschlagen.';
                } elseif ((int)($user['account_locked'] ?? 0) === 1) {
                    $error = 'Dein Account ist gesperrt. Bitte prüfe deine E-Mails.';
                } else {

                    /* =========================================
                       LOGIN HISTORY / DEVICE
                    ========================================= */
                    $ipHash = hash('sha256', $ip . env('APP_KEY'));
                    $uaHash = hash('sha256', $ua);
                    $deviceHash = hash('sha256', $ipHash . $uaHash);

                    Database::execute(
                        'INSERT INTO login_history (user_id, ip_hash, ua_hash)
                         VALUES (?, ?, ?)',
                        [$user['id'], $ipHash, $uaHash]
                    );

                    $recent = Database::fetchColumn(
                        'SELECT COUNT(*)
                         FROM login_history
                         WHERE user_id = ?
                           AND created_at > (NOW() - INTERVAL 10 MINUTE)',
                        [$user['id']]
                    );

                    if ((int)$recent >= 3) {

                        $token = bin2hex(random_bytes(32));

                        Database::execute(
                            'UPDATE users
                             SET account_locked = 1,
                                 unlock_token = ?,
                                 unlock_token_expires = DATE_ADD(NOW(), INTERVAL 30 MINUTE)
                             WHERE id = ?',
                            [$token, $user['id']]
                        );

                        MailService::sendAccountUnlockMail(
                            $user['email'],
                            $user['username'],
                            $token
                        );

                        $error = 'Ungewöhnliche Login-Aktivität. Account gesperrt.';
                    } else {

                        /* =========================================
                           SESSION (RBAC-SAUBER)
                        ========================================= */
                        session_regenerate_id(true);

                        $_SESSION['user'] = [
                            'id'         => (int)$user['id'],
                            'username'   => $user['username'],
                            'email'      => $user['email'],
                            'role_id'    => (int)$user['role_id'],
                            'role_name'  => $user['role_name'],
                            'role_level' => (int)$user['role_level'],
                        ];

                        if ($rememberDevice) {
                            Database::execute(
                                'INSERT IGNORE INTO remembered_devices (user_id, device_hash)
                                 VALUES (?, ?)',
                                [$user['id'], $deviceHash]
                            );
                        }

                        Database::execute(
                            'UPDATE users
                             SET last_login_at = NOW(),
                                 last_login_ip_hash = ?,
                                 last_login_ua_hash = ?
                             WHERE id = ?',
                            [$ipHash, $uaHash, $user['id']]
                        );

                        /* =========================================
                           REDIRECT (PERMISSION-BASIERT)
                        ========================================= */
                        if (Permission::has('admin.access')) {
                            header('Location: /admin');
                        } else {
                            header('Location: /dashboard');
                        }
                        exit;
                    }
                }
            }
        }

        View::render('auth/login', [
            'title' => 'Login',
            'error' => $error
        ]);
    }

    /* =========================================================
       LOGOUT
    ========================================================= */
    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    /* =========================================================
       REGISTRIERUNG (FORM)
    ========================================================= */
    public function register(): void
    {
        View::render('auth/register', [
            'title' => 'Registrieren'
        ]);
    }

    /* =========================================================
       AGB AKZEPTIEREN
    ========================================================= */
    public function acceptAgb(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        Database::execute(
            'UPDATE users
             SET agb_accepted_at = NOW()
             WHERE id = ?',
            [$_SESSION['user']['id']]
        );

        header('Location: /dashboard');
        exit;
    }

    /* =========================================================
       E-MAIL BESTÄTIGEN
    ========================================================= */
    public function confirmEmail(): void
    {
        $token = $_GET['token'] ?? '';

        $user = Database::fetch(
            'SELECT id FROM users
             WHERE email_verify_token = ?
               AND email_verified_at IS NULL
             LIMIT 1',
            [$token]
        );

        if (!$user) {
            View::render('errors/419', ['title' => 'Link ungültig']);
            return;
        }

        Database::execute(
            'UPDATE users
             SET email_verified_at = NOW(),
                 email_verify_token = NULL
             WHERE id = ?',
            [$user['id']]
        );

        View::render('auth/email_confirmed', [
            'title' => 'E-Mail bestätigt'
        ]);
    }

    /* =========================================================
       ACCOUNT ENTPERREN
    ========================================================= */
    public function unlockAccount(): void
    {
        $token = $_GET['token'] ?? '';

        $user = Database::fetch(
            'SELECT id FROM users
             WHERE unlock_token = ?
               AND unlock_token_expires > NOW()
             LIMIT 1',
            [$token]
        );

        if (!$user) {
            View::render('errors/419', ['title' => 'Link ungültig']);
            return;
        }

        Database::execute(
            'UPDATE users
             SET account_locked = 0,
                 unlock_token = NULL,
                 unlock_token_expires = NULL
             WHERE id = ?',
            [$user['id']]
        );

        View::render('auth/account_unlocked', [
            'title' => 'Account entsperrt'
        ]);
    }
}
