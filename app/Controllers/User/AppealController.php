<?php
declare(strict_types=1);

class AppealController
{
    public function store(): void
    {
        Security::requireAuth();
        Security::checkCsrf();

        $actionId = (int)($_POST['action_id'] ?? 0);
        $message  = trim($_POST['message'] ?? '');

        if ($actionId && $message !== '') {
            Appeal::create(
                Auth::user()->username,
                $actionId,
                $message
            );
        }

        Response::redirect('/profile');
    }
}
