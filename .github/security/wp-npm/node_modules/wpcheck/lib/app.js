
/**
 * Required modules
 */

const request = require( 'request' ).defaults( { timeout: 9999 } )
const fs = require( './fs' )
const url = require( './url' )
const log = require( './log' )
const config = require( '../config/app.json' )


/**
 * Initiator method
 *
 * @param   {Object}  data  Initial data
 * @return  void
 */

module.exports.wpcheck = ( data ) => {

    // App version
    if ( data.v ) {
        return require( './version' )
    }

    // App help
    if ( data.h ) {
        return require( './help' )
    }

    // Bulk file
    if ( data.b ) {
        try {
            data._.push( ...fs.readFileLines( data.b ) )
        } catch( error ) {
            log.warn( error )
        }
    }

    // Loop sources
    return data._.forEach( url => {

        init( {
            'wpURL': url,
            'siteURL': url,
            'rulesDir': data.r,
            'userAgent': data.u,
            'ignoreRule': data.i,
            'silentMode': data.s
        } ).then( data => {

            return lookupSiteURL( data )

        } ).then( data => {

            return lookupWpURL( data )

        } ).then( data => {

            return loadRules( data )

        } ).catch( error => {

            return log.warn( error )

        } )

    } )

}


/**
 * Validate URl and start lookup
 *
 * @param   {Object}  data  Initial data
 * @return  void
 */

const init = ( data ) => {

    return new Promise( ( resolve, reject ) => {

        // Init siteURL
        const siteURL = url.normalize( data.siteURL )

        // Invalid URL?
        if ( ! siteURL ) {
            return reject( new Error( `${data.siteURL} is not a valid URL` ) )
        }

        // Save URLs
        data.siteURL = data.wpURL = siteURL

        // Resolve data
        return resolve( data )

    } )

}


/**
 * Lookup for the site URL
 *
 * @param   {Object}  data  Working data
 * @return  void
 */

const lookupSiteURL = ( data ) => {

    return new Promise( ( resolve, reject ) => {

        // Constants from data
        const { siteURL, userAgent, silentMode } = data

        // Request
        request( {
            'url': siteURL,
            'method': 'HEAD',
            'headers': { 'User-Agent': userAgent }
        }, ( error, response ) => {

            // Handle errors
            if ( error ) {
                return reject( new Error( `Can not resolve ${siteURL} (${error.message})` ) )
            }

            // Status code not OK
            if ( response.statusCode !== 200 ) {
                return reject( new Error( `Can not resolve ${siteURL} (${response.statusCode} status code)` ) )
            }

            // Override siteURL
            if ( url.hasRedirects( response ) ) {
                const finalURL = url.getRedirect( response )

                if ( finalURL ) {
                    data.siteURL = data.wpURL = finalURL

                    log.info( `New site URL: ${siteURL} \u2192 ${finalURL}`, { silentMode } )
                }
            }

            // Resolve data
            return resolve( data )

        } )

    } )

}


/**
 * Lookup for the WordPress URL
 *
 * @param   {Object}  data  Working data
 * @return  void
 */

const lookupWpURL = ( data ) => {

    return new Promise( ( resolve, reject ) => {

        // Constants from data
        const { wpURL, siteURL, userAgent, silentMode } = data

        // Test file URL
        const targetURL = siteURL + config.testFile

        // Request
        request( {
            'url': targetURL,
            'method': 'HEAD',
            'headers': { 'User-Agent': userAgent }
        }, ( error, response ) => {

            // Extract URL from page content
            if ( error || response.statusCode !== 200 ) {
                return extractWpURL( data ).then( data => {

                    return resolve( data )

                } ).catch( error => {

                    return reject( error ) // new Error() already called in extractWpURL()

                } )
            }

            // Override wpURL
            if ( url.hasRedirects( response ) ) {
                const finalURL = url.getRedirect( response )

                if ( finalURL ) {
                    data.wpURL = finalURL

                    // Small talk
                    log.info( `New WordPress URL: ${wpURL} \u2192 ${finalURL}`, { silentMode } )
                }
            }

            // Resolve data
            return resolve( data )

        } )

    } )
}


/**
 * Extract WordPress URL from page content
 *
 * @param   {Object}  data  Working data
 * @return  void
 */

const extractWpURL = ( data ) => {

    return new Promise( ( resolve, reject ) => {

        // Constants from data
        const { wpURL, siteURL, userAgent, silentMode } = data

        // Request
        request( {
            'url': wpURL,
            'method': 'GET',
            'headers': { 'User-Agent': userAgent }
        }, ( error, response, body ) => {

            // Handle errors
            if ( error || response.statusCode !== 200 ) {
                return reject( new Error( `${siteURL} is not using WordPress (response error)` ) )
            }

            // Identifier not found
            if ( ! body.includes('/wp-') ) {
                return reject( new Error( `${siteURL} is not using WordPress (no references to wp-*)` ) )
            }

            // Regexp discovery
            const [ , parsedURL ] = body.match( /["'](https?[^"']+)\/wp-(?:content|includes)/ ) || []

            // Unescape URL
            const finalURL = url.normalize( parsedURL )

            // Validate URL
            if ( ! finalURL ) {
                return reject( new Error( `${siteURL} is not using WordPress (no valid references)` ) )
            }

            // Override wpURL
            data.wpURL = finalURL

            // Small talk
            log.info( `New WordPress URL: ${wpURL} \u2192 ${finalURL}`, { silentMode } )

            // Resolve data
            return resolve( data )

        } )

    } )
}


/**
 * Load module rules from rules folder
 *
 * @param   {Object}  data  Working data
 * @return  void
 */

const loadRules = ( data ) => {

    // Default rules dir
    let dirPaths = [ config.rulesDir ]

    // Custom rules dir
    if ( data.rulesDir ) {
        dirPaths.push( data.rulesDir )
    }

    // Loop available dirs
    dirPaths.forEach( dirPath => {

        fs.readDir( dirPath, ( error, filePaths ) => {

            if ( error ) {
                return log.warn( error )
            }

            filePaths.map( filePath => {

                return fs.joinPaths( dirPath, filePath )

            } ).filter( filePath => {

                return fs.isFile( filePath, '.js' ) && ! fs.isBlacklistedFile( filePath, data.ignoreRule )

            } ).forEach( filePath => {

                try {
                    return fs.requireFile( filePath ).fire( data )
                } catch( error ) {
                    return log.warn( error )
                }

            } )

        } )

    } )

}
