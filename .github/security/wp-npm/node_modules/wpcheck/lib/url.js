
/**
 * Required modules
 */

const rtrim = require( 'rtrim' )
const prependHttp = require( 'prepend-http' )
const validUrl = require( 'valid-url' ).isWebUri


module.exports = {


    /**
     * Normalize and validate a URL
     *
     * @param   {String}  url  Incoming URL
     * @return  {Mixed}        Valid URL or false if invalid
     */

    normalize( url ) {

        if ( ! url ) {
            return false
        }

        let cleanURL = rtrim( prependHttp( url ), '/' ).replace( /\\/g, '' ) // trim() by prependHttp

        if ( validUrl( cleanURL ) ) {
            return cleanURL
        }

        return false

    },


    /**
     * Checks for existing redirects based on [request] Node.js module
     *
     * @param   {String}  url  Incoming URL
     * @return  {boolean}              true → has redirects, false → no redirects
     */

    hasRedirects( url ) {

        return !! url.request._redirect.redirects.length

    },


    /**
     * Returns final redirect URL based on [request] Node.js module
     *
     * @param   {String}  url  Incoming URL
     * @return  {String}               Redirect URL
     */

    getRedirect( url ) {

        return this.normalize( url.request.uri.href )

    }

}
