<?php

/**
 * Template Name: Tree View
 * Description: Tree View will be displayed
 */

if (isset($_GET['term_id']) && !empty($_GET['term_id'])) {
    $term_id = sanitize_text_field($_GET["term_id"]);
    if($term_id) :
        $data = Tips_API_Common::fetch_tree_view_data($term_id);
    endif;  
    if (is_array($data)) {
        $json = json_encode($data);
        $result = json_decode($json);
?>
    <div style="width: 100%; height: 1200px;">
        <canvas id="canvas"></canvas>
        <select id="orientation" value="horizontal">
            <option>horizontal</option>
            <option>radial</option>
        </select>
    </div>
    <div style="clear:both;margin-bottom:64px">&nbsp;</div>


    <script type="text/javascript">
        const nodes = <?php echo json_encode($result); ?>;

        const chart = new Chart(document.getElementById('canvas').getContext('2d'), {
            type: 'dendrogram',
            plugins: [ChartDataLabels],
            data: {
                labels: nodes.map((d) => d.name),
                datasets: [{
                    pointBackgroundColor: 'steelblue',
                    pointRadius: 5,
                    // stepped: 'middle',
                    data: nodes,
                }, ],
            },
            options: {
                // animation: false,
                maintainAspectRatio: false,
                layout: {
                    padding: 128
                },
                onClick: (e) => {
                    console.log(e);
                },
                plugins: {
                    datalabels: {
                        display: true,
                        clamp: true,
                        align: 'left',
                        offset: 6,
                        backgroundColor: 'white',
                        formatter: (v) => {
                            let output = v.name;

                            if (v.original !== undefined) {
                                output = output + '\n' + v.original;
                            }
                            return output;
                        },
                    },
                    legend: {
                        display: false,
                    }
                },
            },
        });

        document.getElementById('orientation').onchange = (evt) => {
            chart.getDatasetMeta(0).controller.reLayout({
                orientation: evt.currentTarget.value,
            });
        };
    </script>
<?php
}}
else{?>
  <div class="entry-content">
     <section class="no-sources-section">
            <div class="entry-content">
                <?php esc_html_e('No Treee Record Found.', 'tips-find-ferse-within-site'); ?>
            </div>
        </section>
</div>  
<?php }
?>