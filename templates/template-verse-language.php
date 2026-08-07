<?php

/**
 * Template Name: language Details Page
 * Description: language Details will be displayed
 */

$terms = isset($_GET['languageId']) ? sanitize_text_field($_GET['languageId']) : '';
$page  = isset($_GET['pages']) ? (int) sanitize_text_field($_GET['pages']) : 1;

$api_response = [];
if ($terms) :
    $api_response = Tips_API_Common::fetch_language_stories_from_api($terms, $page);
endif;

// ── Separate named sections from the stories array ───────────────────────────
$language_info = isset($api_response['get_language_info']) ? $api_response['get_language_info'] : null;
$verse_data    = isset($api_response['VerseData'])         ? $api_response['VerseData']         : null;
$stories       = isset($api_response['stories'])           ? $api_response['stories']           : [];
$pagination    = isset($api_response['pagination'])        ? $api_response['pagination']        : null;

$url = site_url();
?>
<div class="container entry-content inner-page-design language-page">

    <?php if ($terms && !empty($stories)) : ?>

        <div class="language-page-wrapper">

            <?php /* ══════════════════════════════
                   LEFT — Main content column
               ══════════════════════════════ */ ?>
            <div class="language-main-content">

                <?php /* ── Language info block ── */ ?>
                <section class="language-info-section">

                    <?php /* Title — "LANGUAGE: ADAMAWA FULFULDE" */ ?>
                    <?php if (!empty($verse_data['title'])) : ?>
                        <h1 class="verse-title">
                            <?php esc_html_e('Language:', 'tips-find-ferse-within-site'); ?>
                            <?php echo esc_html($verse_data['title']); ?>
                        </h1>
                    <?php endif; ?>

                    <?php /* Description line */ ?>
                    <?php if (!empty($language_info['title'])) : ?>
                        <p class="language-description">
                            <?php echo esc_html($language_info['title']); ?>
                        </p>
                    <?php endif; ?>

                    <?php /* Breadcrumb — Niger-Congo > Atlantic-Congo > Atlantic > ... */ ?>
                    <?php if (!empty($language_info['breadcrumb'])) : ?>
                        <nav class="language-breadcrumb" aria-label="Language family">
                            <?php $crumb_count = count($language_info['breadcrumb']); ?>
                            <?php foreach ($language_info['breadcrumb'] as $index => $crumb) : ?>
                                <?php if ($crumb['url']) : ?>
                                    <a href="<?php echo esc_url($url . $crumb['url']); ?>">
                                        <?php echo esc_html($crumb['title']); ?>
                                    </a>
                                <?php else : ?>
                                    <span><?php echo esc_html($crumb['title']); ?></span>
                                <?php endif; ?>
                                <?php if ($index < $crumb_count - 1) : ?>
                                    <span class="breadcrumb-sep">&gt;</span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </nav>
                    <?php endif; ?>

                </section>

                <?php /* ── "Language-specific Insights" heading ── */ ?>
                <h2 class="language-stories-heading">
                    <?php esc_html_e('Language-specific Insights', 'tips-find-ferse-within-site'); ?>
                </h2>

                <?php /* ── Stories loop ── */ ?>
                <section class="book-stories-section">

                    <?php foreach ($stories as $verse) : ?>
                        <article class="book-story">

                            <?php /* Story title */ ?>
                            <?php if (!empty($verse['title']['rendered'])) : ?>
                                <a href="<?php echo esc_url($verse['title']['title_link']); ?>">
                                    <h2>
                                        <?php echo wp_kses_post(sanitize_text_field($verse['title']['rendered'])); ?>
                                        <?php if (!empty($verse['title']['hover_title'])) : ?>
                                            <span class="term with-original"
                                                  data-original="<?php echo esc_attr($verse['title']['hover_title']); ?>">
                                                <?php echo wp_kses_post(sanitize_text_field($verse['title']['hover_title'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    </h2>
                                </a>
                            <?php endif; ?>

                            <?php /* Geographical / tree link */ ?>
                            <?php if (!empty($verse['geographical_link']['title'])) : ?>
                                <p class="tree-link">
                                    <a href="<?php echo esc_url($url . $verse['geographical_link']['link']); ?>">
                                        <?php echo esc_html($verse['geographical_link']['title']); ?>
                                    </a>
                                </p>
                            <?php endif; ?>

                            <?php /* Post content */ ?>
                            <?php if (!empty($verse['content']['rendered'])) : ?>
                                <div class="entry-content">
                                    <?php echo wp_kses_post($verse['content']['rendered']); ?>
                                </div>
                            <?php endif; ?>

                            <?php /* Translation details */ ?>
                            <?php if (!empty($verse['translation_details'])) : ?>
                                <div class="language-content">
                                    <?php echo wp_kses_post($verse['translation_details']); ?>
                                </div>
                            <?php endif; ?>

                            <?php /* Translation data */ ?>
                            <?php if (!empty($verse['translation_data'])) : ?>
                                <div class="translation-data">
                                    <?php echo wp_kses_post($verse['translation_data']); ?>
                                </div>
                            <?php endif; ?>

                        </article>
                    <?php endforeach; ?>

                    <?php /* ── Pagination ── */ ?>
                    <?php if ($pagination && $pagination['total_pages'] > 1) : ?>
                        <?php
                        $total_pages  = (int) $pagination['total_pages'];
                        $image_url    = $pagination['image_url'];
                        $base         = $pagination['base'];
                        $current_page = isset($_GET['pages']) ? (int) $_GET['pages'] : 1;
                        $htmlpage     = Tips_API_Common::custom_paginate_links(
                            $current_page,
                            $total_pages,
                            $url,
                            $image_url,
                            $base
                        );
                        ?>
                        <div class="pagination">
                            <?php echo wp_kses_post($htmlpage); ?>
                        </div>
                    <?php endif; ?>

                </section>

            </div><!-- /.language-main-content -->

            <?php /* ══════════════════════════════
                   RIGHT — Sidebar (MORE INFORMATION)
                   Each section is independent —
                   map disabled does NOT hide links
               ══════════════════════════════ */ ?>
            <?php
            $has_map     = !empty($language_info['map']['latitude']) && !empty($language_info['map']['longitude']);
            $has_wiki    = !empty($language_info['wikipedia']['url']);
            $has_bible   = !empty($language_info['online_bible']['url']);
            $show_sidebar = $language_info && ($has_map);
            ?>
            <?php if ($show_sidebar) : ?>
                <aside class="language-sidebar">
                    <div class="sidebar-box">

                        <p class="sidebar-title">
                            <?php esc_html_e('More Information', 'tips-find-ferse-within-site'); ?>
                        </p>

                        <?php /* ── Map — only if lat/lng available ── */ ?>
                        <?php if ($has_map) : ?>
                            <div id="language-leaflet-map"></div>
                        <?php endif; ?>

                        <?php /* ── Wikipedia + Online Bible — independent of map ── */ ?>
                        <?php if ($has_wiki || $has_bible) : ?>
                            <div class="sidebar-links">

                                <?php if ($has_wiki) : ?>
                                    <a href="<?php echo esc_url($language_info['wikipedia']['url']); ?>"
                                       target="_blank" rel="noopener noreferrer">
                                        <?php echo esc_html($language_info['wikipedia']['title']); ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                            <polyline points="15 3 21 3 21 9"/>
                                            <line x1="10" y1="14" x2="21" y2="3"/>
                                        </svg>
                                    </a>
                                <?php endif; ?>

                                <?php if ($has_bible) : ?>
                                    <a href="<?php echo esc_url($language_info['online_bible']['url']); ?>"
                                       target="_blank" rel="noopener noreferrer">
                                        <?php echo esc_html($language_info['online_bible']['title']); ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                            <polyline points="15 3 21 3 21 9"/>
                                            <line x1="10" y1="14" x2="21" y2="3"/>
                                        </svg>
                                    </a>
                                <?php endif; ?>

                            </div><!-- /.sidebar-links -->
                        <?php endif; ?>

                    </div><!-- /.sidebar-box -->
                </aside>
            <?php endif; ?>

        </div><!-- /.language-page-wrapper -->

    <?php else : ?>

        <section class="no-stories-section">
            <div class="entry-content">
                <p><?php esc_html_e('No Sources Found.', 'tips-find-ferse-within-site'); ?></p>
            </div>
        </section>

    <?php endif; ?>

</div>

<?php /* ── Leaflet JS — only when map data exists ── */ ?>
<?php if (!empty($language_info['map']['latitude']) && !empty($language_info['map']['longitude'])) : ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    var lat = <?php echo floatval($language_info['map']['latitude']); ?>;
    var lng = <?php echo floatval($language_info['map']['longitude']); ?>;

    if (lat && lng) {
        // Custom marker icon — same as live site
        var customIcon = L.icon({
            iconUrl:     'https://tips.translation.bible/wp-content/uploads/2026/05/marker.png',
            iconSize:    [25, 25],
            iconAnchor:  [0, 32],
            popupAnchor: [0, -32]
        });

        // Initialize map — target the sidebar div id
        var map = L.map('language-leaflet-map', {
            fullscreenControl: true
        }).setView([lat, lng], 4);

        // OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Marker at language location
        var marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);

        // Popup on map click — shows clicked coordinates
        var popup = L.popup();
        map.on('click', function(e) {
            var clickedLat = e.latlng.lat.toFixed(5);
            var clickedLng = e.latlng.lng.toFixed(5);

            marker.setLatLng([clickedLat, clickedLng]);

            popup
                .setLatLng([clickedLat, clickedLng])
                .setContent('Coordinates:<br> ' + clickedLat + ' ,' + clickedLng)
                .openOn(map);
        });
    }
    </script>
<?php endif; ?>