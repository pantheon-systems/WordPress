CLI_ACCEPT_COOKIE_NAME =(typeof CLI_ACCEPT_COOKIE_NAME !== 'undefined' ? CLI_ACCEPT_COOKIE_NAME : 'viewed_cookie_policy');
CLI_ACCEPT_COOKIE_EXPIRE =(typeof CLI_ACCEPT_COOKIE_EXPIRE !== 'undefined' ? CLI_ACCEPT_COOKIE_EXPIRE : 365);
CLI_COOKIEBAR_AS_POPUP=(typeof CLI_COOKIEBAR_AS_POPUP !== 'undefined' ? CLI_COOKIEBAR_AS_POPUP : false);
var CLI_Cookie={
	set: function (name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
        } else
            var expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
        if(days<1)
        {
            host_name=window.location.hostname;
            document.cookie = name + "=" + value + expires + "; path=/; domain=."+host_name+";";
            host_name=host_name.substring(host_name.lastIndexOf(".", host_name.lastIndexOf(".")-1));
            document.cookie = name + "=" + value + expires + "; path=/; domain="+host_name+";";
        }
    },
    read: function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    },
    erase: function (name) {
        this.set(name, "", -10);
    },
    exists: function (name) {
        return (this.read(name) !== null);
    },
    getallcookies:function() 
    {
	    var pairs = document.cookie.split(";");
	    var cookieslist = {};
	    for (var i = 0; i < pairs.length; i++) {
	        var pair = pairs[i].split("=");
	        cookieslist[(pair[0] + '').trim()] = unescape(pair[1]);
	    }
	    return cookieslist;
	}
}
var CLI=
{
	bar_config:{},
	showagain_config:{},
	set:function(args)
	{
		if(typeof JSON.parse !== "function") 
		{
	        console.log("CookieLawInfo requires JSON.parse but your browser doesn't support it");
	        return;
	    }
	    this.settings = JSON.parse(args.settings);
	    this.bar_elm=jQuery(this.settings.notify_div_id);
	    this.showagain_elm = jQuery(this.settings.showagain_div_id);

        //buttons
        this.main_button=jQuery('.cli-plugin-main-button');
        this.main_link = jQuery('.cli-plugin-main-link');
        this.reject_link = jQuery('.cookie_action_close_header_reject');
        this.delete_link=jQuery(".cookielawinfo-cookie-delete");

        if(this.settings.as_popup)
    	{
    		CLI_COOKIEBAR_AS_POPUP=true;
    	}
        this.configShowAgain();
        this.configBar();
        this.attachStyles();
        this.toggleBar();
        this.attachDelete();
        this.attachEvents();
        this.configButtons();
        if(this.settings.scroll_close === true) 
        {
        	window.addEventListener("scroll",CLI.closeOnScroll, false);
    	}
	},
	attachEvents:function()
	{
		jQuery('.cli_action_button').click(function(e){
			e.preventDefault();
			var elm=jQuery(this);
			var button_action=elm.attr('data-cli_action');
			var open_link=elm[0].hasAttribute("href") && elm.attr("href") != '#' ? true : false;
			var new_window=false;
			if(button_action=='accept')
			{
				CLI.accept_close();
				new_window=CLI.settings.button_1_new_win ? true : false;
			}else if(button_action=='reject')
			{
				CLI.reject_close();
				new_window=CLI.settings.button_3_new_win ? true : false;
			}
			CLI.saveLog(button_action);
			if(open_link)
			{
                if(new_window)
                {
                    window.open(elm.attr("href"),'_blank');
                }else
                {
                    window.location.href =elm.attr("href");
                }  
            }
		});
	},
	saveLog:function(button_action)
	{
		if(CLI.settings.logging_on)
		{
			jQuery.ajax({
	            url: log_object.ajax_url,
	            type: 'POST',
	            data:{
	                action: 'wt_log_visitor_action',
	                wt_clicked_button_id: '',
	                wt_user_action:button_action,
	                cookie_list:CLI_Cookie.getallcookies()
	            },
	            success:function (response)
	            {
	               
	            }
	        });
		}
	},
	attachDelete:function()
	{
		this.delete_link.click(function () {
	        CLI_Cookie.erase(CLI_ACCEPT_COOKIE_NAME);
	        for(var k in Cli_Data.nn_cookie_ids) 
	        {
	            CLI_Cookie.erase(Cli_Data.nn_cookie_ids[k]);
	        }
	        return false;
	    });
	},
	configButtons:function()
	{
	    //[cookie_button]
	    this.main_button.css('color',this.settings.button_1_link_colour);
	    if(this.settings.button_1_as_button) 
	    {
	        this.main_button.css('background-color',this.settings.button_1_button_colour);
	        this.main_button.hover(function () {
	            jQuery(this).css('background-color',CLI.settings.button_1_button_hover);
	        },function (){
	            jQuery(this).css('background-color',CLI.settings.button_1_button_colour);
	        });
	    }

	    //[cookie_link]	    
	    this.main_link.css('color',this.settings.button_2_link_colour);
	    if(this.settings.button_2_as_button) 
	    {
	        this.main_link.css('background-color',this.settings.button_2_button_colour);
	        this.main_link.hover(function () {
	            jQuery(this).css('background-color',CLI.settings.button_2_button_hover);
	        },function (){
                jQuery(this).css('background-color',CLI.settings.button_2_button_colour);
            });
	    }


	    //[cookie_reject]	    
	    this.reject_link.css('color',this.settings.button_3_link_colour);
	    if(this.settings.button_3_as_button) 
	    {
	        this.reject_link.css('background-color',this.settings.button_3_button_colour);
	        this.reject_link.hover(function () {
	            jQuery(this).css('background-color',CLI.settings.button_3_button_hover);
	        },function () {
	            jQuery(this).css('background-color',CLI.settings.button_3_button_colour);
	        });
	    }
	},
	toggleBar:function()
	{
		if(CLI_COOKIEBAR_AS_POPUP)
		{
			this.barAsPopUp();
		}
		if(!CLI_Cookie.exists(CLI_ACCEPT_COOKIE_NAME)) 
		{
	        this.displayHeader();
	    } else {
	        this.bar_elm.hide();
	    }
	    if(this.settings.show_once_yn) 
	    {
	        setTimeout(function(){
	        	CLI.close_header();
	        },CLI.settings.show_once);
	    }

	    this.showagain_elm.click(function (e) {
	        e.preventDefault();
	        CLI.showagain_elm.slideUp(CLI.settings.animate_speed_hide,function() 
	        {
	            CLI.bar_elm.slideDown(CLI.settings.animate_speed_show);
	            if(CLI_COOKIEBAR_AS_POPUP)
				{
					CLI.showPopupOverlay();
				}
	        });
	    });
	},
	attachStyles:function()
	{
		this.bar_elm.css(this.bar_config).hide();
		this.showagain_elm.css(this.showagain_config);
	},
	configShowAgain:function()
	{
		this.showagain_config = {
	        'background-color': this.settings.background,
	        'color':this.l1hs(this.settings.text),
	        'position': 'fixed',
	        'font-family': this.settings.font_family
	    };
	    if(this.settings.border_on) 
	    {
	        var border_to_hide = 'border-' + this.settings.notify_position_vertical;
	        this.showagain_config['border'] = '1px solid ' + this.l1hs(this.settings.border);
	        this.showagain_config[border_to_hide] = 'none';
	    }
	    if(this.settings.notify_position_horizontal == "left") 
	    {
	        this.showagain_config.left = this.settings.showagain_x_position;
	    }else if(this.settings.notify_position_horizontal == "right") 
	    {
	        this.showagain_config.right = this.settings.showagain_x_position;
	    }
	},
	configBar:function()
	{
		this.bar_config = {
	        'background-color':this.settings.background,
	        'color':this.settings.text,
	        'font-family':this.settings.font_family
	    };
	    if(this.settings.notify_position_vertical=="top") 
	    {
	        this.bar_config['top'] = '0';
	        if(this.settings.header_fix === true) 
	        {
	            this.bar_config['position'] = 'fixed';
	        }
	    }else 
	    {
	        this.bar_config['bottom'] = '0';
	    }
	    if(this.settings.notify_position_vertical == "top") 
	    {
	        if(this.settings.border_on) 
	        {
	            this.bar_config['border-bottom'] = '2px solid ' +this.l1hs(this.settings.border);
	        }
	        this.showagain_config.top = '0';
	    }
	    else if(this.settings.notify_position_vertical == "bottom") 
	    {
	        if(this.settings.border_on) 
	        {
	            this.bar_config['border-top'] = '2px solid ' + this.l1hs(this.settings.border);
	        }
	        this.bar_config['position'] = 'fixed';
	        this.bar_config['bottom'] = '0';
	        this.showagain_config.bottom = '0';
	    }
	},
	l1hs:function(str) 
	{
	    if (str.charAt(0) == "#") {
	        str = str.substring(1, str.length);
	    } else {
	        return "#" + str;
	    }
	    return this.l1hs(str);
	},
	close_header:function() 
	{
        CLI_Cookie.set(CLI_ACCEPT_COOKIE_NAME,'yes',CLI_ACCEPT_COOKIE_EXPIRE);
        this.hideHeader();
    },
	accept_close:function() 
    {        
        this.hidePopupOverlay();
        CLI_Cookie.set(CLI_ACCEPT_COOKIE_NAME,'yes',CLI_ACCEPT_COOKIE_EXPIRE);
        if(this.settings.notify_animate_hide) 
        {
            this.bar_elm.slideUp(this.settings.animate_speed_hide);
        }else 
        {
            this.bar_elm.hide();
        }
        this.showagain_elm.slideDown(this.settings.animate_speed_show);
        if(this.settings.accept_close_reload === true) 
        {
            window.location.reload();
        }
        return false;
    },
	reject_close:function() 
    {
        this.hidePopupOverlay();
        for(var k in Cli_Data.nn_cookie_ids) 
        {
            CLI_Cookie.erase(Cli_Data.nn_cookie_ids[k]);
        }
        CLI_Cookie.set(CLI_ACCEPT_COOKIE_NAME,'no',CLI_ACCEPT_COOKIE_EXPIRE);
        if(this.settings.notify_animate_hide) 
        {
            this.bar_elm.slideUp(this.settings.animate_speed_hide);
        } else 
        {
            this.bar_elm.hide();
        }
        this.showagain_elm.slideDown(this.settings.animate_speed_show);
        if(this.settings.reject_close_reload === true) 
        {
            window.location.reload();
        }
        return false;
    },
	closeOnScroll:function() 
	{
        if(window.pageYOffset > 100 && !CLI_Cookie.read(CLI_ACCEPT_COOKIE_NAME)) 
        {
            CLI.accept_close();
            if(CLI.settings.scroll_close_reload === true) 
            {
                window.location.reload();
            }
            window.removeEventListener("scroll",CLI.closeOnScroll,false);
        }
    },
    displayHeader:function() 
    {   
        if(this.settings.notify_animate_show) 
        {
            this.bar_elm.slideDown(this.settings.animate_speed_show);
        }else 
        {
            this.bar_elm.show();
        }
        this.showagain_elm.hide();
        if(CLI_COOKIEBAR_AS_POPUP)
		{
			this.showPopupOverlay();
		}    
    },
    hideHeader:function()
    {       
        if(this.settings.notify_animate_show) 
        {
            this.showagain_elm.slideDown(this.settings.animate_speed_show);
        } else {
            this.showagain_elm.show();
        }
        this.bar_elm.slideUp(this.settings.animate_speed_show);
        this.hidePopupOverlay();
    },
    hidePopupOverlay:function() 
    {
        jQuery('body').removeClass("cli-barmodal-open");
        jQuery(".cli-popupbar-overlay").removeClass("cli-show");
    },
    showPopupOverlay:function()
    {
        if(this.settings.popup_overlay)
        {
        	jQuery('body').addClass("cli-barmodal-open");
        	jQuery(".cli-popupbar-overlay").addClass("cli-show");
    	}
    },
    barAsPopUp:function()
    {    	
    	if(typeof cookie_law_info_bar_as_popup==='function')
    	{
    		return false;
    	}
    	var cli_elm=this.bar_elm;
	    var cli_win=jQuery(window);
	    var cli_winh=cli_win.height()-40;
	    var cli_winw=cli_win.width();
	    var cli_defw=cli_winw>700 ? 500 : cli_winw-20;
	    //var cli_defw=cli_defw<500 ? 500 : cli_defw;

	    cli_elm.css({
	        'width':cli_defw,'height':'auto','max-height':cli_winh,'bottom':'','top':'50%','left':'50%','margin-left':(cli_defw/2)*-1,'margin-top':'-100px','padding':'25px 15px','overflow':'auto'
	    }).addClass('cli-bar-popup cli-modal-content');
	    cli_elm.append('<div style="width:100%; padding-top:15px; float:left; display:block" class="cli_pop_btn_container"></div>');
	    cli_elm.find('a').appendTo('.cli_pop_btn_container');
	    cli_elm.find('a').css({'margin-top':'10px','margin-left':'5px'});
	    cli_elm.find('span').css({'float':'left','display':'block','width':'100%','height':'auto','max-height':(cli_winh-100),'overflow':'auto','text-align':'left'});
	    
	    cli_h=cli_elm.height();
	    li_h=cli_h<200 ? 200 : cli_h;
	    cli_elm.css({'top':'50%','margin-top':((cli_h/2)+30)*-1});	    
	    setTimeout(function(){ 
		    cli_elm.css({
		        'bottom':''
		    });
	     },100);
    }
}
jQuery(document).ready(function() {
    if(typeof cli_cookiebar_settings!='undefined')
    {
	    CLI.set({
	      settings:cli_cookiebar_settings
	    });
	}
});