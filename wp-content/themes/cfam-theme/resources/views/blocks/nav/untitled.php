?>


<section class="tab-container home-hero-data  counter-section bg-gray">
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <h4>Cantor Fitzgerald Sustainable Infrastructure Fund</h4>
            </div>
            <div class="col-lg-4 d-flex justify-content-lg-end mt-4 mt-lg-0">
                <?php if (get_field('fact_sheet_link')) : ?>
                <a href="<?php the_field('fact_sheet_link'); ?>" type="button" class="btn btn-labeled btn-link p-0"
                    download>
                    <span class="btn-label label-before">
                        <i class="icon-file"></i>
                    </span>
                    Fact Sheet
                </a>
                <?php endif; ?>

                <?php if (get_field('prospectus_link')) : ?>
                <a href="<?php the_field('prospectus_link'); ?>" type="button" class="btn btn-labeled btn-link p-0 ms-4"
                    download>
                    <span class="btn-label label-before">
                        <i class="icon-file"></i>
                    </span>
                    Prospectus
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div id="tabs-nav-select" class="home-hero-data-dropdown d-lg-flex align-items-center">
            <?php
                #check if data is not open
                $trow = 1;
                $csv = array();

                if (!empty($daily_web_site_file)) {
                    echo "<select class='form-select form-select-lg' aria-label=''>";
                    foreach ($daily_web_site_file as $text) {
                        if ($trow == 1) {
                            echo "<option value='#" . $trow . "' selected>" . $text[4] . "</option>\n";
                        } else {
                            echo "<option value='#" . $trow . "'>" . $text[4] . "</option>\n";
                        }
                        $trow++;
                    }
                    echo '</select>';
                }
                ?>
            <p class="text-paragraph--small mt-2 mt-lg-0 ms-lg-3">
                As of <?php echo $text[0]; ?>
        </div>

        <?php

            $drow = 1;
            if (!empty($daily_web_site_file)) :
                foreach ($daily_web_site_file as $text) :
                    $nav = $text[5];
            ?>
        <div id='<?php echo $drow; ?>' class='tab-content'>
            <div class="home-hero-data-table d-lg-flex justify-content-lg-between">
                <div class="hero-data-table-item">
                    <p class="text-eyebrow text-uppercase text-blue2">
                        Nav
                    </p>
                    <div class="d-flex align-items-center mt-2">
                        <h3 class="text-statistics">
                            <sup class="d-none">$</sup>
                            <?php echo $nav; ?>
                        </h3>
                    </div>
                </div>
                <div class="hero-data-table-item">
                    <p class="text-eyebrow text-uppercase text-blue2">
                        1 Day NAV Change
                    </p>
                    <div class="d-flex align-items-center mt-2">
                        <h3 class="text-statistics">
                            <?php echo ($text[7]  * 100); ?><sup>%</sup>
                        </h3>
                    </div>
                </div>
                <div class="hero-data-table-item">
                    <p class="text-eyebrow text-uppercase text-blue2">
                        YTD Total Return
                    </p>
                    <div class="d-flex align-items-center mt-2">
                        <h3 class="text-statistics">
                            <?php echo ($text[9] * 100); ?><sup>%</sup>
                        </h3>
                    </div>
                </div>
                <div class="hero-data-table-item d-none">
                    <p class="text-eyebrow text-uppercase text-blue2">Annualized Distribution Rate (NAV)
                    </p>
                    <div class="d-flex align-items-center mt-2">
                        <h3 class="text-statistics">
                            <?php echo ($text[9] * $text[5]); ?><sup>%</sup>
                        </h3>
                        <div class="tooltip ms-3"><i class="icon-info"></i>
                            <span tooltiptext class="tooltiptext">Distribution rates are not guaranteed. They are
                                calculated by
                                annualizing the most recent distribution per share and diving by the NAV as of the
                                reported date.<br /><br />
                                Distributions may be comprised of ordinary income, net capital gains, and/or a return of
                                capital (ROC) of your investment in the fund. Because the distribution rate may include
                                a ROC, it should not be confused with yield or income. </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
                    $drow++;
                endforeach;
            endif; ?>
    </div>
</section>

<?php