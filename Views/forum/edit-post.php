<?php
/**
 * Forum – Beitrag bearbeiten
 *
 * Erwartete Variablen:
 * @var array       $post
 * @var string|null $error
 */

$post  = is_array($post ?? null) ? $post : [];
$error = $error ?? null;

$postId   = (int) ($post['id'] ?? 0);
$threadId = (int) ($post['thread_id'] ?? 0);
$content  = $post['content'] ?? '';
?>

<main>
    <section class="forum-wrapper">
        <div class="forum-container">

            <header class="forum-header">
                <h1 class="forum-title">✏️ Beitrag bearbeiten</h1>
            </header>

            <?php if ($postId === 0 || $threadId === 0): ?>

                <div class="forum-error">
                    Der zu bearbeitende Beitrag konnte nicht gefunden werden.
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="/forum" class="forum-btn secondary">Zurück zum Forum</a>
                </div>

            <?php else: ?>

                <?php if (!empty($error)): ?>
                    <div class="forum-error">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="post"
                      action="/forum/post/<?= $postId ?>/update"
                      class="forum-form">

                    <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">

                    <div class="forum-form-group">
                        <span>Inhalt</span>
                        <textarea
                            name="content"
                            rows="8"
                            required
                            placeholder="Beitrag bearbeiten…"
                        ><?= htmlspecialchars($content, ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="forum-form-actions">
                        <button type="submit" class="forum-btn">💾 Speichern</button>
                        <a href="/forum/thread/<?= $threadId ?>" class="forum-btn secondary">Abbrechen</a>
                    </div>

                </form>

            <?php endif; ?>

        </div>
    </section>
</main>