<?php

class HardwareController
{
    /* =========================================================
       HARDWARE – FRONTEND (MEHRERE SETUPS)
       URLs:
       /hardware
       /hardware/streaming
       /hardware/office
       /hardware/mobile
    ========================================================= */

    public function index(?string $slug = null): void
    {
        // -----------------------------------------
        // Setup ermitteln
        // -----------------------------------------

        if ($slug) {
            $setup = Database::fetch(
                "SELECT * FROM hardware_setups WHERE slug = ?",
                [$slug]
            );
        } else {
            $setup = Database::fetch(
                "SELECT * FROM hardware_setups WHERE is_active = 1"
            );
        }

        if (!$setup) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Hardware-Setup nicht gefunden']);
            return;
        }

        // -----------------------------------------
        // Alle Setups (für Switcher im Frontend)
        // -----------------------------------------

        $setups = Database::fetchAll(
            "SELECT * FROM hardware_setups ORDER BY id ASC"
        );

        // -----------------------------------------
        // Hardware-Items laden
        // -----------------------------------------

        $items = Database::fetchAll(
            "SELECT *
             FROM hardware_items
             WHERE setup_id = ?
             ORDER BY category ASC, sort ASC",
            [$setup['id']]
        );

        // -----------------------------------------
        // Items nach Kategorien gruppieren
        // -----------------------------------------

        $hardware = [
            'pc'               => [],
            'monitors'         => [],
            'audio'            => [],
            'camera_lighting'  => [],
            'extras'           => [],
        ];

        foreach ($items as $item) {
            switch ($item['category']) {
                case 'pc':
                    $hardware['pc'][] = $item;
                    break;

                case 'monitors':
                    $hardware['monitors'][] = $item;
                    break;

                case 'audio':
                    $hardware['audio'][] = $item;
                    break;

                case 'camera':
                case 'camera_lighting':
                    $hardware['camera_lighting'][] = $item;
                    break;

                case 'extras':
                    $hardware['extras'][] = $item;
                    break;
            }
        }

        // -----------------------------------------
        // View rendern
        // -----------------------------------------

        View::render('hardware/index', [
            'title'    => 'Hardware – ' . $setup['title'],
            'setup'    => $setup,
            'setups'   => $setups,
            'hardware' => $hardware
        ]);
    }
}
