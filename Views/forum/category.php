<?php
/**
 * Forum – Kategorieansicht (Thread-Liste)
 *
 * Erwartete Variablen:
 * @var array  $category
 * @var array  $threads
 * @var string $title
 * @var string $currentPage
 */

$category = is_array($category ?? null) ? $category : [];
$threads  = is_array($threads  ?? null) ? $threads  : [];

$categoryTitle = $category['title'] ?? '';
$categorySlug  = $category['slug'] ?? '';
$categoryDesc  = $category['description'] ?? '';
?>

<section class="forum-wrapper">
    <div class="forum-container">

        <!-- =========================================================
             HEADER / HERO
        ========================================================= -->
        <header class="forum-header forum-hero">

            <span class="forum-eyebrow">Forum</span>

            <h1 class="forum-title">
                <?= htmlspecialchars($categoryTitle, ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <?php if ($categoryDesc !== ''): ?>
                <p class="forum-subtitle">
                    <?= htmlspecialchars($categoryDesc, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <div class="forum-breadcrumb">
                <a href="/forum">Forum</a>
                <span>›</span>
                <span><?= htmlspecialchars($categoryTitle, ENT_QUOTES, 'UTF-8') ?></span>
            </div>

            <!-- ACTIONS -->
            <div class="forum-actions">
                <?php if (!empty($_SESSION['user']) && $categorySlug !== ''): ?>
                    <a
                        href="/forum/<?= htmlspecialchars($categorySlug, ENT_QUOTES, 'UTF-8') ?>/create"
                        class="forum-btn"
                    >
                        + Neues Thema
                    </a>
                <?php else: ?>
                    <span class="forum-login-hint">
                        <a href="/login">Anmelden</a>, um ein neues Thema zu erstellen
                    </span>
                <?php endif; ?>
            </div>

        </header>

        <!-- =========================================================
             EMPTY STATE
        ========================================================= -->
        <?php if (empty($threads)): ?>

            <section class="forum-empty forum-empty-hero">
                <div class="forum-empty-inner">

                    <span class="forum-empty-icon">🔥</span>

                    <h2>Keine Themen vorhanden</h2>

                    <p>
                        In dieser Kategorie wurden noch keine Threads erstellt.<br>
                        Starte die erste Diskussion.
                    </p>

                    <?php if (!empty($_SESSION['user']) && $categorySlug !== ''): ?>
                        <a
                            href="/forum/<?= htmlspecialchars($categorySlug, ENT_QUOTES, 'UTF-8') ?>/create"
                            class="forum-btn"
                        >
                            Erstes Thema erstellen
                        </a>
                    <?php else: ?>
                        <p class="forum-login-hint">
                            <a href="/login">Anmelden</a>, um das erste Thema zu eröffnen
                        </p>
                    <?php endif; ?>

                </div>
            </section>

        <!-- =========================================================
             THREAD LIST
        ========================================================= -->
        <?php else: ?>

            <div class="forum-threads">

                <?php foreach ($threads as $thread): ?>

                    <?php
                        $threadId    = (int) ($thread['id'] ?? 0);
                        $threadTitle = $thread['title'] ?? '';
                        $username    = $thread['username'] ?? 'Unbekannt';
                        $createdAt   = $thread['created_at'] ?? null;
                        $replyCount  = (int) ($thread['reply_count'] ?? 0);
                        $viewCount   = (int) ($thread['view_count'] ?? 0);
                    ?>

                    <a href="/forum/thread/<?= $threadId ?>"
                       class="forum-thread-card">

                        <div class="forum-thread-main">

                            <h3 class="forum-thread-title">
                                <?= htmlspecialchars($threadTitle, ENT_QUOTES, 'UTF-8') ?>

                                <?php if (!empty($thread['is_sticky'])): ?>
                                    <span class="thread-sticky" title="Wichtig">📌</span>
                                <?php endif; ?>

                                <?php if (!empty($thread['is_locked'])): ?>
                                    <span class="thread-locked" title="Geschlossen">🔒</span>
                                <?php endif; ?>
                            </h3>

                            <div class="forum-thread-meta">
                                <span>
                                    von <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span>•</span>
                                <span>
                                    <?= $createdAt
                                        ? htmlspecialchars(
                                            date('d.m.Y H:i', strtotime($createdAt)),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )
                                        : '—'
                                    ?>
                                </span>

                                <?php if ($replyCount > 0 || $viewCount > 0): ?>
                                    <span>•</span>
                                    <span class="forum-thread-stats">
                                        <?php if ($replyCount > 0): ?>
                                            💬 <?= $replyCount ?>
                                        <?php endif; ?>
                                        <?php if ($viewCount > 0): ?>
                                            👁 <?= $viewCount ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>

                        <div class="forum-thread-arrow">→</div>

                    </a>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
</section>
