<?php
    $new_products_query_sale = tep_db_query("
      select
        p.products_id,
        pd.products_viewed,
        p.products_image_thumbnail,
        p.products_image,
        p.products_tax_class_id,
        pd.products_name,
        if(s.status, s.specials_new_products_price, p.products_price) as products_price
      from
        " . TABLE_PRODUCTS . " p
            left join
        " . TABLE_SPECIALS . " s
            on
        p.products_id = s.products_id,
        " . TABLE_PRODUCTS_DESCRIPTION . " pd
    where
        p.products_status = 1
            and
        p.products_id = pd.products_id
            and
        p.products_kind_of = 'For Sale'
            and
        pd.language_id = '" . (int)$languages_id . "'
            order by
        p.products_promote desc, p.products_date_added desc
        limit " . MAX_DISPLAY_NEW_PRODUCTS
    );

  $num_new_products_sale = tep_db_num_rows($new_products_query_sale);

  if ($num_new_products_sale > 0) {

    $new_prods_content = NULL;

    while ($new_products_sale = tep_db_fetch_array($new_products_query_sale)) {
      if (strlen($new_products_sale['products_name']) > 35) {
        $p_name = substr($new_products_sale['products_name'], 0, 40) . '...';
      }else{
        $p_name = $new_products_sale['products_name'];
      }
      $new_prods_content_sale .= '

        <div class="item">
          <!-- col-md-12 -->
          <div class="col-md-12 rent-block">
            <!-- Property Main Box -->
            <div class="property-main-box">
              <div class="property-images-box">
                <a
                    title="Property Image"
                    href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products_sale['products_id']) . '"
                >
                '
                    . tep_image(DIR_WS_IMAGES . $new_products_sale['products_image_thumbnail'],
              $new_products_sale['products_name'], SMALL_IMAGE_WIDTH,
                    SMALL_IMAGE_HEIGHT, 'style="width:100%; height: 170px;"') .
                '
                    </a>
                <h4>
                    ' . $currencies->display_price($new_products_sale['products_price'], tep_get_tax_rate($new_products_sale['products_tax_class_id'])) . '
                </h4>
              </div>
              <div class="clearfix"></div>
              <div class="property-details">
                <a title="Property Title" href="index.html#">' . $p_name . '</a>
                <ul>
                  <li><i class="fa fa-expand"></i>3326 sq</li>
                  <li><i><img src="images/icon/bed-icon.png" alt="bed-icon" /></i>3</li>
                  <li><i><img src="images/icon/bath-icon.png" alt="bath-icon" /></i>2</li>
                </ul>
              </div>
            </div><!-- Property Main Box /- -->
          </div><!-- col-md-12 /- -->
       </div>
          ';
    }
?>
    <!-- Rent Property -->
    <div class="sale-property">
    <div class="col-md-6 rent">
      <div class="section-header">
        <h3><span><?php echo PROPERTY; ?></span><?php echo SALE;?></h3>
      </div>
    </div>
    <div class="col-md-12 p_r_z">
    <div id="sale-property-block" class="sale-property-block">
      <?php echo $new_prods_content_sale; ?>
    </div>
    </div>
  </div><!-- Rent Property /- -->
<?php
  }