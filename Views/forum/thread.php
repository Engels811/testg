<?php
/**
 * Forum – Threadansicht
 *
 * Erwartete Variablen:
 * @var array $thread
 * @var array $posts
 */

$thread = is_array($thread ?? null) ? $thread : [];
$posts  = is_array($posts  ?? null) ? $posts  : [];

$threadId    = (int) ($thread['id'] ?? 0);
$threadTitle = $thread['title'] ?? '';
$threadUser  = $thread['username'] ?? 'Unbekannt';
$threadDate  = $thread['created_at'] ?? null;
$isLocked    = !empty($thread['is_locked']);
$isSticky    = !empty($thread['is_sticky']);

$userRole = $_SESSION['user']['role'] ?? null;
$isStaff  = in_array($userRole, ['admin', 'moderator'], true);
?>

<main>
    <section class="forum-wrapper">
        <div class="forum-container">

            <header class="forum-header">
                <h1 class="forum-title">
                    <?= htmlspecialchars($threadTitle, ENT_QUOTES, 'UTF-8') ?>
                </h1>

                <div class="forum-thread-meta">
                    von <?= htmlspecialchars($threadUser, ENT_QUOTES, 'UTF-8') ?>
                    •
                    <?= $threadDate
                        ? htmlspecialchars(date('d.m.Y H:i', strtotime($threadDate)), ENT_QUOTES, 'UTF-8')
                        : '—'
                    ?>

                    <?php if ($isLocked): ?>
                        • 🔒 Geschlossen
                    <?php endif; ?>
                </div>

                <?php if (!empty($_SESSION['user']) && $isStaff && $threadId > 0): ?>
                    <div class="forum-admin-actions">
                        <form method="post" action="/admin/forum/toggle-sticky">
                            <input type="hidden" name="csrf" value="<?= Security::csrf() ?>">
                            <input type="hidden" name="id" value="<?= $threadId ?>">
                            <button type="submit" class="forum-admin-btn-sticky">
                                <?= $isSticky ? '📌 Unsticky' : '📌 Sticky' ?>
                            </button>
                        </form>

                        <form method="post" action="/admin/forum/toggle-lock">
                            <input type="hidden" name="csrf" value="<?= Security::csrf() ?>">
                            <input type="hidden" name="id" value="<?= $threadId ?>">
                            <button type="submit" class="forum-admin-btn-lock">
                                <?= $isLocked ? '🔓 Öffnen' : '🔒 Sperren' ?>
                            </button>
                        </form>

                        <form method="post"
                              action="/admin/forum/delete"
                              onsubmit="return confirm('Thread wirklich löschen?')">
                            <input type="hidden" name="csrf" value="<?= Security::csrf() ?>">
                            <input type="hidden" name="id" value="<?= $threadId ?>">
                            <button type="submit" class="forum-admin-btn-delete">
                                🗑 Löschen
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

            </header>

            <div class="forum-posts">

                <?php if (empty($posts)): ?>

                    <div class="forum-empty">
                        <h2>Noch keine Beiträge</h2>
                        <p>Sei der Erste, der in diesem Thread antwortet.</p>
                    </div>

                <?php else: ?>

                    <?php foreach ($posts as $post): ?>

                        <?php
                            $postId    = (int) ($post['id'] ?? 0);
                            $username  = $post['username'] ?? 'Unbekannt';
                            $content   = $post['content'] ?? '';
                            $createdAt = $post['created_at'] ?? null;
                            // Avatar-Pfad überprüfen und Fallback verwenden, falls nicht vorhanden
                            $avatar    = $post['avatar'] ?? '/uploads/avatars/default.png';
                            $online    = !empty($post['is_online']);
                        ?>

                        <article class="forum-post <?= !empty($post['is_new']) ? 'new' : '' ?>">

                            <div class="forum-post-avatar">
                                <!-- Avatar-Bild mit Fallback auf Standard-Bild -->
                                <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar">
                                <span class="forum-user-status <?= $online ? '' : 'offline' ?>"></span>
                            </div>

                            <div class="forum-post-body">

                                <div class="forum-post-header">
                                    <span class="forum-post-author">
                                        <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
                                    </span>

                                    <span class="forum-post-date">
                                        <?= $createdAt
                                            ? htmlspecialchars(date('d.m.Y H:i', strtotime($createdAt)), ENT_QUOTES, 'UTF-8')
                                            : '—'
                                        ?>
                                    </span>
                                </div>

                                <div class="forum-post-content" id="post-<?= $postId ?>">
                                    <?= nl2br(htmlspecialchars($content, ENT_QUOTES, 'UTF-8')) ?>
                                </div>

                                <?php if (!empty($post['attachments']) && is_array($post['attachments'])): ?>
                                    <div class="forum-attachments">
                                        <?php foreach ($post['attachments'] as $a): ?>
                                            <?php if (!empty($a['file_path'])): ?>
                                                <div class="forum-attachment">
                                                    <img
                                                        src="<?= htmlspecialchars($a['file_path'], ENT_QUOTES, 'UTF-8') ?>"
                                                        loading="lazy"
                                                        class="lightbox-trigger"
                                                        alt="Attachment">
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="forum-post-actions">
                                    <button
                                        class="forum-post-action quote-btn"
                                        data-username="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                                        data-post-id="<?= $postId ?>">
                                        💬 Zitieren
                                    </button>
                                </div>

                            </div>
                        </article>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

            <?php if (!empty($_SESSION['user']) && !$isLocked && $threadId > 0): ?>

                <section class="forum-reply-wrapper">

                    <h2 class="forum-reply-title">Antwort schreiben</h2>

                    <form method="post"
                          action="/forum/thread/<?= $threadId ?>/reply"
                          enctype="multipart/form-data"
                          class="forum-reply-card">

                        <input type="hidden" name="csrf" value="<?= Security::csrf() ?>">

                        <textarea
                            name="content"
                            required
                            placeholder="Deine Antwort..."></textarea>

                        <div class="forum-reply-actions">

                            <label class="forum-upload">
                                📎 Bilder auswählen
                                <input type="file" name="attachments[]" multiple accept="image/*" hidden>
                            </label>

                            <button type="submit" class="forum-btn">
                                Antwort absenden
                            </button>

                        </div>

                    </form>

                </section>

            <?php elseif ($isLocked): ?>

                <div class="forum-locked-hint">
                    🔒 Dieser Thread ist geschlossen.
                </div>

            <?php endif; ?>

        </div>
    </section>
</main>

<script src="/assets/js/forum-lightbox.js" defer></script>
<script src="/assets/js/forum-reply.js" defer></script>
