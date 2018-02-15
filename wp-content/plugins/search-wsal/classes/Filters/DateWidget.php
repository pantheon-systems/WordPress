<?php

class WSAL_AS_Filters_DateWidget extends WSAL_AS_Filters_AbstractWidget
{
    protected function RenderField()
    {
        $date_format = WSAL_SearchExtension::GetInstance()->GetDateFormat();
        ?><input type="text"
           class="<?php echo $this->GetSafeName(); ?>"
           id="<?php echo esc_attr($this->id); ?>"
           placeholder="<?php echo $date_format; ?>"
           data-prefix="<?php echo esc_attr($this->prefix); ?>"/>
        <?php
    }
    
    public function StaFooter()
    {
        ?><script type="text/javascript">
            window.WsalAs.Attach(function(){
                jQuery('input.<?php echo $this->GetSafeName(); ?>').change(function(){
                    if(this.value){
                        WsalAs.AddFilter(jQuery(this).attr('data-prefix') + ':' + this.value);
                        this.value = '';
                    }
                });
            });
        </script><?php
    }
}
