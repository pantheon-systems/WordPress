    # just in case the request snippet for x-pass is not set we pass here
    if ( req.http.x-pass ) {
        return(pass);
    }

    /* handle 5XX (or any other unwanted status code) */
    if (beresp.status >= 500 && beresp.status < 600) {

        /* deliver stale if the object is available */
        if (stale.exists) {
        return(deliver_stale);
        }

        if (req.restarts < 1 && (req.request == "GET" || req.request == "HEAD")) {
        restart;
        }

    }
