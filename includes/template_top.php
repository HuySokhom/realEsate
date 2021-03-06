<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  $oscTemplate->buildBlocks();

  if (!$oscTemplate->hasBlocks('boxes_column_left')) {
    $oscTemplate->setGridContentWidth($oscTemplate->getGridContentWidth() + $oscTemplate->getGridColumnWidth());
  }

  if (!$oscTemplate->hasBlocks('boxes_column_right')) {
    $oscTemplate->setGridContentWidth($oscTemplate->getGridContentWidth() + $oscTemplate->getGridColumnWidth());
  }
?>
<!DOCTYPE html>
<html
    <?php echo HTML_PARAMS; ?>
    xmlns:ng="http://angularjs.org/"
    data-ng-app="main"
>
    <head>
        <meta charset="<?php echo CHARSET; ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo tep_output_string_protected($oscTemplate->getTitle()); ?></title>
        <base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
        <meta
            name="description"
            http-equiv="Description"
            content="singa property, real property, phnom penh, real estate in cambodia, news,
                <?php
                    echo tep_output_string_protected($oscTemplate->getTitle());
                    echo ',';
                    echo tep_output_string_protected($oscTemplate->getContent());
                ?>
            ">
        <meta name="keywords" content="singa property, real property, phnom penh, real estate in cambodia, news, <?php echo tep_output_string_protected($oscTemplate->getTitle()); ?>">
        <meta name="author" content="Singa property">
        <link
            rel="canonical"
            href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>"
        >
        <meta http-equiv="ROBOTS" content="INDEX, FOLLOW">
        <link rel="shortcut icon" href="images/banners/ico.png">
        <link href="themes/libraries/bootstrap/bootstrap.min.css" rel="stylesheet"/>
        <linK href="themes/libraries/owl-carousel/owl.carousel.css" rel="stylesheet"/>
        <linK href="themes/libraries/owl-carousel/owl.theme.css" rel="stylesheet"/>
        <link href="themes/style.css" rel="stylesheet"/>
        <link href="themes/css/media.css" rel="stylesheet"/>
    <!--[if lt IE 9]>
       <script src="ext/js/html5shiv.js"></script>
       <script src="ext/js/respond.min.js"></script>
       <script src="ext/js/excanvas.min.js"></script>
    <![endif]-->
        <link href='//fonts.googleapis.com/css?family=Khmer:400normal|Didact+Gothic:400normal|Open+Sans:400normal|Handlee:400normal|Lato:400normal|Lora:400normal|Roboto:400normal|Nunito:400normal|Montserrat:400normal|Hanuman:400normal|Raleway:400normal&subset=all' rel="stylesheet" type="text/css">
        <script src="ext/jquery/jquery-1.11.1.min.js"></script>
    <!--    <meta property="og:title" content="Singa Property Online Real Estate in Cambodia | Buy &amp;amp; Sell Online on Real Estate Singa.com" />-->
    <!--    <meta property="og:locale" content="en_US" />-->
    <!--    <meta property="og:type" content="Website" />-->
    <!--    <meta property="og:description" content="Welcome to Singa Online Property"/>-->
    <!--    <meta property="og:url" content="http://singaproperty.com"/>-->
    <!--    <meta property="og:site_name" content="SINGA PROPERTY Online Real Estate"/>-->
    <!-- font awesome -->
        <link rel="stylesheet" href="ext/css/Font-Awesome-master/css/font-awesome.min.css">
        <meta name="csrf-param" content="authenticity_token" />
        <meta name="csrf-token" content="sNMk592JV2wwHn6DPJ8C5oy/hHDnjIlZBOHyngtTbpQ=" />
        <?php echo $oscTemplate->getBlocks('header_tags'); ?>
    </head>
    <body
        onload="initialize()"
        class=""
    >
    <input type="text" id="language_id" value="<?php echo $_SESSION['languages_id']; ?>" hidden>
    <input type="text" id="customers_plan" value="<?php echo $_SESSION['customer_plan']; ?>" hidden>
    <input type="text" id="customers_limit_products" value="<?php echo $_SESSION['customers_limit_products']; ?>" hidden>
    <input type="text" id="customers_expire_plan" value="<?php echo $_SESSION['plan_expire']; ?>" hidden>
    <input type="text" id="url" value="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>" hidden>
      <?php require(DIR_WS_INCLUDES . 'header.php');
        $url = $_SERVER['REQUEST_URI'];
          $fullUrl = end( (explode('/', $url)) );
          // check url if is index page
          if( strpos($fullUrl,'index.php') !== false || $fullUrl == '' || strpos($fullUrl,'index') !== false){
            $image_slider_query = tep_db_query("
                select
                  text, image
                from
                  image_slider
                where
                  image != ''
                    order by
                  sort_order asc limit 10
               ");
            if (tep_db_num_rows($image_slider_query) > 0) {
                include(DIR_WS_INCLUDES . 'slider.php');
            }
        }
      ?>
    <!-- Go to www.addthis.com/dashboard to customize your tools -->
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-57849c71a603c3db"></script>
    <div class="scrollPost" style="display: none;">
        <a href="account.php#/manage_property/post" title="Post Property" id="upload-property">
            <i class="fa fa-cloud-upload"></i>
            Upload Your Property Now
        </a>
    </div>