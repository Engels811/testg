<?php
/**
 * Forum – Suche
 *
 * Erwartete Variablen:
 * @var string|null $query
 * @var array|null  $results
 * @var string|null $error
 */

$query   = is_string($query ?? null) ? trim($query) : '';
$results = is_array($results ?? null) ? $results : [];
$error   = $error ?? null;
?>

<main>
    <section class="forum-wrapper forum-search">
        <div class="forum-container">

            <header class="forum-header">
                <h1 class="forum-title">🔍 Forum durchsuchen</h1>
                <p class="forum-subtitle">
                    Threads und Beiträge im Forum finden
                </p>
            </header>

            <form method="get" action="/forum/search" class="forum-search-form">

                <input
                    type="text"
                    name="q"
                    placeholder="Suchbegriff eingeben…"
                    value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

                <button type="submit" class="forum-btn">Suchen</button>
            </form>

            <?php if (!empty($error)): ?>
                <div class="forum-error">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if ($query !== '' && !empty($results)): ?>

                <h2 class="forum-search-count">
                    <?= count($results) ?> Treffer gefunden
                </h2>

                <ul class="forum-search-results">

                    <?php foreach ($results as $r): ?>

                        <?php
                            $threadId    = (int) ($r['thread_id'] ?? 0);
                            $threadTitle = $r['thread_title'] ?? '';
                            $username    = $r['username'] ?? '';
                            $createdAt   = $r['created_at'] ?? null;
                            $excerpt     = $r['excerpt'] ?? '';
                        ?>

                        <?php if ($threadId === 0 || $threadTitle === ''): continue; endif; ?>

                        <li class="forum-search-item">

                            <a href="/forum/thread/<?= $threadId ?>" class="forum-search-title">
                                <?= htmlspecialchars($threadTitle, ENT_QUOTES, 'UTF-8') ?>
                            </a>

                            <div class="forum-search-meta">
                                von <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
                                ·
                                <?= $createdAt
                                    ? htmlspecialchars(
                                        date('d.m.Y H:i', strtotime($createdAt)),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                    : '—'
                                ?>
                            </div>

                            <?php if ($excerpt !== ''): ?>
                                <p class="forum-search-excerpt">
                                    <?= htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            <?php endif; ?>

                        </li>

                    <?php endforeach; ?>

                </ul>

            <?php elseif ($query !== '' && empty($results)): ?>

                <div class="forum-empty">
                    <h2>Keine Treffer</h2>
                    <p>
                        Für <strong><?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?></strong>
                        wurden keine Ergebnisse gefunden.
                    </p>
                </div>

            <?php endif; ?>

        </div>
    </section>
</main>