# If we are about to serve a 5xx we need to restart then in vcl_recv error out to
# get the holding page
if ( resp.status >= 500 && resp.status < 600 && !req.http.ResponseObject ) {
    set req.http.ResponseObject = "WORDPRESS_ERROR_PAGE";
    restart;
}
