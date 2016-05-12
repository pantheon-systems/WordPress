// @VERSION 4.0

// need to define this first for some reason - rraub1
var ASUHeader = ASUHeader || {};

// Variables
//ASUHeader.inIFrame = (window.top != window) ? true : false;

// SSO Methods
ASUHeader.alterLoginHref = function(url) {
    if (ASUHeader.signin_url != '') {
        return ASUHeader.signin_url;
    }
    
    if (ASUHeader.signin_callback_url == '') {
      ASUHeader.signin_callback_url = window.location.toString();
      // Depricated default_iframe header ID support for 4.0 and 4.1 because browsers don't allow us to run window.parent.location.toString(); statement within iframe. --> Uncommented since we need default_iframe and default_myasu_iframe for 4.0. (2/18/2013)
      if (ASUHeader.inIFrame) {
        try {
          // If we're in an iFrame, force the document domain to be asu.edu
          document.domain = 'asu.edu';
          ASUHeader.signin_callback_url = window.parent.location.toString();
        } catch(ignore) {}
      }
    }
    
    // Decode the URL just in case
    url = unescape(url);
    // set the callapp into the url
    url = url.replace('**w.l**', encodeURIComponent(ASUHeader.signin_callback_url));
    
    ASUHeader.signin_url = url;
    return ASUHeader.signin_url;
}

ASUHeader.checkSSOCookie = function() {
    // try to parse out the username from SSONAME cookie
    var cookies = document.cookie.split(";");
    for(var i = 0; i < cookies.length; i++) {
        if (cookies[i].indexOf('SSONAME') > 0) {
            if (cookies[i].substring(9) == "") {
                break;
            }
            ASUHeader.user_displayname = cookies[i].substring(9);
            ASUHeader.user_signedin = true;
            
            break;
        }
    }
}

ASUHeader.setSSOLink = function() {
  // break out if the correct variables are not set or if the user is not signed in
  if (typeof ASUHeader.user_signedin == "undefined" || ASUHeader.user_signedin == false) {
    return;
  }
  
  var ul = document.getElementById('asu_login_module');
  while (ul.childNodes[0]) {
      ul.removeChild(ul.childNodes[0]);
  }
  
  if (ASUHeader.user_displayname) {
    var sso_name = document.createElement('li');
    //sso_name.innerHTML = ASUHeader.user_displayname; --- Security fix suggested by Jason Harper on 5/3/2013
    sso_name.appendChild(document.createTextNode(ASUHeader.user_displayname));
    ul.appendChild(sso_name);
  }
  
  var sso_link = document.createElement('li');
  sso_link.innerHTML = '<a target="_top" href="'+ASUHeader.signout_url+'">SIGN OUT</a>';
  sso_link.className = 'end';
  sso_link.id = 'asu_hdr_sso';
  ul.appendChild(sso_link);
  
  if (document.getElementById('myasu_bar') != null) {
    document.getElementById('myasu_bar').style.display = "block";
  }
  
}

// Search Methods
ASUHeader.searchSwitch = function(name) {
    var field = document.getElementById('asu_search_box');
    if (field != null) {
        var oldDefault = ASUHeader.default_search_text;
        ASUHeader.default_search_text = "Search "+name;
        if (field.value == oldDefault) {
            field.value = ASUHeader.default_search_text;
        }
    }
}
ASUHeader.searchFocus = function(field) {
    if (typeof field != "undefined") {
        if (field.value == ASUHeader.default_search_text) {
            field.value = "";
        }
    }
}
ASUHeader.searchBlur = function(field) {
    if (typeof field != "undefined") {
        if (field.value == "") {
            field.value = ASUHeader.default_search_text;
        }
    }
}
ASUHeader.searchToggle = function(radio) {
    var google = document.getElementById('asu_search_google');
    if (google != null) {
        if (google.style.display == "none") {
            ASUHeader.default_search_text = "Search ASU";
            google.style.display = "block";
        } else {
            google.style.display = "none";
        }
    }
    var alt = document.getElementById('asu_search_alternate');
    if (alt != null) {
        if (alt.style.display == "none") {
            ASUHeader.default_search_text = ASUHeader.default_search_alttext;
            alt.style.display = "block";
        } else {
            alt.style.display = "none";
        }
    }

    if (typeof radio != "undefined") {
        radio.checked = false;
    }
}
