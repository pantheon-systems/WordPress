<?php add_thickbox();?>
<div class="wrap">
    <div id="icon-users" class="icon32"><br/></div>
    <?php if( ! empty( $available_tabs ) ): ?>
        <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
            <?php foreach( $available_tabs as $id => $label ): ?>
                <a href="?page=<?php echo $page ?>&tab=<?php echo $id ?>" class="nav-tab <?php echo ( $current_tab == $id ) ? 'nav-tab-active' : '' ?>"><?php echo $label ?></a>
            <?php endforeach; ?>
        </h2>
        <?php $this->print_panel_content() ?>
    <?php endif; ?>
</div>