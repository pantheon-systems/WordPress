<?php global $YWCM_Instance; ?>
<style>
    .section{
        margin-left: -20px;
        margin-right: -20px;
        font-family: "Raleway",san-serif;
    }
    .section h1{
        text-align: center;
        text-transform: uppercase;
        color: #808a97;
        font-size: 35px;
        font-weight: 700;
        line-height: normal;
        display: inline-block;
        width: 100%;
        margin: 50px 0 0;
    }
    .section:nth-child(even){
        background-color: #fff;
    }
    .section:nth-child(odd){
        background-color: #f1f1f1;
    }
    .section .section-title img{
        display: table-cell;
        vertical-align: middle;
        width: auto;
        margin-right: 15px;
    }
    .section h2,
    .section h3 {
        display: inline-block;
        vertical-align: middle;
        padding: 0;
        font-size: 24px;
        font-weight: 700;
        color: #808a97;
        text-transform: uppercase;
    }

    .section .section-title h2{
        display: table-cell;
        vertical-align: middle;
    }

    .section-title{
        display: table;
    }

    .section h3 {
        font-size: 14px;
        line-height: 28px;
        margin-bottom: 0;
        display: block;
    }

    .section p{
        font-size: 13px;
        margin: 25px 0;
    }
    .section ul li{
        margin-bottom: 4px;
    }
    .landing-container{
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
        padding: 50px 0 30px;
    }
    .landing-container:after{
        display: block;
        clear: both;
        content: '';
    }
    .landing-container .col-1,
    .landing-container .col-2{
        float: left;
        box-sizing: border-box;
        padding: 0 15px;
    }
    .landing-container .col-1 img{
        width: 100%;
    }
    .landing-container .col-1{
        width: 55%;
    }
    .landing-container .col-2{
        width: 45%;
    }
    .premium-cta{
        background-color: #808a97;
        color: #fff;
        border-radius: 6px;
        padding: 20px 15px;
    }
    .premium-cta:after{
        content: '';
        display: block;
        clear: both;
    }
    .premium-cta p{
        margin: 7px 0;
        font-size: 14px;
        font-weight: 500;
        display: inline-block;
        width: 60%;
    }
    .premium-cta a.button{
        border-radius: 6px;
        height: 60px;
        float: right;
        background: url(<?php echo YITH_YWCM_ASSETS_URL?>/images/upgrade.png) #ff643f no-repeat 13px 13px;
        border-color: #ff643f;
        box-shadow: none;
        outline: none;
        color: #fff;
        position: relative;
        padding: 9px 50px 9px 70px;
    }
    .premium-cta a.button:hover,
    .premium-cta a.button:active,
    .premium-cta a.button:focus{
        color: #fff;
        background: url(<?php echo YITH_YWCM_ASSETS_URL?>/images/upgrade.png) #971d00 no-repeat 13px 13px;
        border-color: #971d00;
        box-shadow: none;
        outline: none;
    }
    .premium-cta a.button:focus{
        top: 1px;
    }
    .premium-cta a.button span{
        line-height: 13px;
    }
    .premium-cta a.button .highlight{
        display: block;
        font-size: 20px;
        font-weight: 700;
        line-height: 20px;
    }
    .premium-cta .highlight{
        text-transform: uppercase;
        background: none;
        font-weight: 800;
        color: #fff;
    }

    @media (max-width: 768px) {
        .section{margin: 0}
        .premium-cta p{
            width: 100%;
        }
        .premium-cta{
            text-align: center;
        }
        .premium-cta a.button{
            float: none;
        }
    }

    @media (max-width: 480px){
        .wrap{
            margin-right: 0;
        }
        .section{
            margin: 0;
        }
        .landing-container .col-1,
        .landing-container .col-2{
            width: 100%;
            padding: 0 15px;
        }
        .section-odd .col-1 {
            float: left;
            margin-right: -100%;
        }
        .section-odd .col-2 {
            float: right;
            margin-top: 65%;
        }
    }

    @media (max-width: 320px){
        .premium-cta a.button{
            padding: 9px 20px 9px 70px;
        }

        .section .section-title img{
            display: none;
        }
    }
</style>
<div class="landing">
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf(__('Upgrade to the %1$spremium version%2$s of %1$sYITH WooCommerce Cart Messages%2$s to benefit from all features!','yith-woocommerce-cart-messages'),'<span class="highlight">','</span>');?>
                </p>
                <a href="<?php echo $YWCM_Instance->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-cart-messages');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-cart-messages');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_YWCM_ASSETS_URL ?>/images/01-bg.png) no-repeat #fff; background-position: 85% 75%">
        <h1>Premium Features</h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/01.png" alt=<?php _e('create message regarding','yith-woocommerce-cart-messages');?> />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/01-icon.png" alt="icon-01"/>
                    <h2><?php _e('CREATE MESSAGES REGARDING A MINIMUM AMOUNT TO SPEND','yith-woocommerce-cart-messages');?></h2>
                </div>
                <p><?php echo sprintf(__('It encourages users to exceed a certain amount of purchases to get a benefit for example, %1$sif you spend more than $100 you will get free shipping%2$s, and it also shows the amount needed to fill the gap.','yith-woocommerce-cart-messages'),'<b>','</b>');?></p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_YWCM_ASSETS_URL ?>/images/02-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/02-icon.png" alt="icon-02" />
                    <h2><?php _e('ANALYZE THE REFERRER');?></h2>
                </div>
                <p><?php echo sprintf(__('It shows a notification based on the source site. For example, %1$sif users come from Google%2$s, you can let them see an additional %1$sdiscount message%2$s.','yith-woocommerce-cart-messages'),'<b>','</b>');?></p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/02.png" alt=<?php _e('analyze the referrer','yith-woocommerce-cart-messages');?> />
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_YWCM_ASSETS_URL ?>/images/03-bg.png) no-repeat #fff; background-position: 85% 100%">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/03.png" alt=<?php _e('create message deadline','yith-woocommerce-cart-messages');?> />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/03-icon.png" alt="icon-03" />
                    <h2><?php _e('CREATE MESSAGES WITH A DEADLINE','yith-woocommerce-cart-messages');?></h2>
                </div>
                <p><?php echo sprintf(__('It lets you set messages with a time limit, for example: %1$s"if you make a purchase before 6 p.m., your order will be shipped today"%2$s.','yith-woocommerce-cart-messages'),'<b>','</b>');?></p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_YWCM_ASSETS_URL ?>/images/04-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/04-icon.png" alt="icon-04" />
                    <h2><?php _e('CHOOSE AMONG DIFFERENT LAYOUTS AND CUSTOMIZE THEM QUICKLY AND EASILY','yith-woocommerce-cart-messages');?></h2>
                </div>
                <p><?php echo sprintf(__('You have %1$s6 different layouts%2$s to give the style you want to your messages from the %1$sadministration panel.%2$s','yith-woocommerce-cart-messages'),'<b>','</b>');?></p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/04.png" alt=<?php _e('choose amoung different','yith-woocommerce-cart-messages');?> />
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_YWCM_ASSETS_URL ?>/images/05-bg.png) no-repeat #fff; background-position: 85% 100%">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/05.png" alt=<?php _e('choose who can see','yith-woocommerce-cart-messages');?> />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWCM_ASSETS_URL?>/images/05-icon.png" alt="icon-05" />
                    <h2><?php _e('CHOOSE WHO CAN SEE YOUR MESSAGES','yith-woocommerce-cart-messages');?></h2>
                </div>
                <p><?php echo sprintf(__('You can show a message to your %1$sclients,%2$s or decide to show it only to %1$sguests%2$s or to logged in %1$susers.%2$s','yith-woocommerce-cart-messages'),'<b>','</b>');?></p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_YWCM_ASSETS_URL ?>/images/06-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/06-icon.png" alt="icon-06" />
                    <h2><?php _e('DECIDE THE PAGES WHERE TO SHOW YOUR MESSAGES','yith-woocommerce-cart-messages');?></h2>
                </div>
                <p><?php echo sprintf(__('You can freely decide where to show every single message %1$s(single page, shop, cart, checkout).%2$s','yith-woocommerce-cart-messages'),'<b>','</b>');?></p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWCM_ASSETS_URL ?>/images/06.png" alt=<?php _e('decide the pages where to','yith-woocommerce-cart-messages');?> />
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf(__('Upgrade to the premium version of YITH WooCommerce Cart Messages to benefit from all features!','yith-woocommerce-cart-messages'),'<span class="highlight">');?>
                </p>
                <a href="<?php echo $YWCM_Instance->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-cart-messages');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-cart-messages');?></span>
                </a>
            </div>
        </div>
    </div>
</div>