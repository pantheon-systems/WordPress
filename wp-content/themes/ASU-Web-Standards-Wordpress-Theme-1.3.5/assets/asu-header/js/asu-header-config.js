// asu-header-config.js
//  to be included after asu-header.js

ASUHeader.default_search_text    = "Search ASU";
ASUHeader.default_search_alttext = "Search ASU";
if (typeof ASUHeader.signin_callback_url == "undefined") {
  ASUHeader.signin_callback_url = '';
}
if (typeof ASUHeader.signin_url == "undefined") {
  ASUHeader.signin_url = '';
}
if (typeof ASUHeader.signout_url == "undefined") {
  ASUHeader.signout_url = 'https://webapp4.asu.edu/myasu/Signout';
}
if (typeof ASUHeader.user_signedin == "undefined" ||
    (ASUHeader.user_signedin != false && typeof ASUHeader.user_displayname == "undefined")) {
  ASUHeader.checkSSOCookie();
}
if (ASUHeader.user_signedin == true) {
  ASUHeader.setSSOLink();
}
if (navigator.userAgent.toLowerCase().indexOf('chrome') > -1) {
    document.getElementById('asu_hdr').className = document.getElementById('asu_hdr').className+" chrome";
}
