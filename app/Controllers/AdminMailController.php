<?php

public function preview(): void
{
    $this->requireAdmin();

    $data = json_decode(file_get_contents('php://input'), true);

    $html = MailTemplate::render('admin_reply', [
        'username'      => 'Vorschau',
        'adminMessage'  => $data['message'] ?? '',
        'siteUrl'       => 'https://engels811-ttv.de'
    ]);

    echo $html;
}
