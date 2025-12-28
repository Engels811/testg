<?php
/**
 * Forum – Kategorienübersicht
 *
 * Erwartete Variablen:
 * @var array  $categories
 * @var string $title
 * @var string $currentPage
 */

$categories = is_array($categories ?? null) ? $categories : [];
?>

<main>
    <section class="forum-wrapper">
        <div class="forum-container">

            <header class="forum-header">
                <h1 class="forum-title">💬 Forum</h1>
                <p class="forum-subtitle">
                    Diskutiere, tausche dich aus und bleib informiert
                </p>
            </header>

            <?php if (empty($categories)): ?>

                <div class="forum-empty">
                    <h2>Noch keine Kategorien</h2>
                    <p>
                        Das Forum wird aktuell eingerichtet.<br>
                        Bitte schaue später erneut vorbei.
                    </p>
                </div>

            <?php else: ?>

                <div class="forum-categories">

                    <?php foreach ($categories as $category): ?>

                        <?php
                            $slug  = $category['slug']  ?? '';
                            $title = $category['title'] ?? '';
                            $desc  = $category['description'] ?? '';
                        ?>

                        <?php if ($slug === '' || $title === ''): continue; endif; ?>

                        <a href="/forum/<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>" 
                           class="forum-category-card">

                            <div class="forum-category-main">
                                <h3 class="forum-category-title">
                                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                                </h3>

                                <?php if ($desc !== ''): ?>
                                    <p class="forum-category-description">
                                        <?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="forum-category-meta">
                                <span class="forum-enter">Betreten →</span>
                            </div>

                        </a>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>
    </section>
</main>