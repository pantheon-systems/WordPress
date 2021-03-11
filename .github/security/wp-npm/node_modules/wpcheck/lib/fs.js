
/**
 * Required modules
 */

const fs = require( 'fs' )
const path = require('path')


module.exports = {


    /**
     * Checks whether the file path is a regular file
     *
     * @param   {String}   filePath  File name
     * @param   {String}   fileExt   File extension
     * @return  {Boolean}  True if a valid file
     */

    isFile( filePath, fileExt ) {

        if ( ! fs.statSync( this.absolutePath( filePath ) ).isFile() ) {
            return false
        }

        if ( fileExt ) {
            return path.extname( filePath ) === fileExt
        }

        return true

    },


    /**
     * Checks whether the dirpath is a regular directory
     *
     * @param   {String}   dirPath  Directory name
     * @return  {Boolean}  True if a valid directory
     */

    isDir( dirPath ) {

        return fs.statSync( this.absolutePath( dirPath ) ).isDirectory()

    },


    /**
     * Reads and returns the content of a file
     *
     * @param   {String}  filePath  File name
     * @return  {Mixed}             File content
     */

    readFile( filePath ) {

        return fs.readFileSync( this.absolutePath( filePath ) )

    },


    /**
     * Reads the content of a directory
     *
     * @param   {String}   dirPath   Directory name
     * @param   {String}   callback  Callback function
     * @return  {Object}             Content object
     */

    readDir( dirPath, callback ) {

        fs.readdir( this.absolutePath( dirPath ), callback )

    },


    /**
     * Reads and returns content lines of a file
     *
     * @param   {String}  filePath  File name
     * @return  {Array}             Content lines
     */

    readFileLines( filePath ) {

        return this.readFile( filePath ).toString().split( "\n" ).filter( Boolean )

    },


    /**
     * Loads a module from a JavaScript file
     *
     * @param   {String}  filePath  File name
     * @return  {Object}            module.exports from the resolved module
     */

    requireFile( filePath ) {

        return require( this.absolutePath( filePath ) )

    },


    /**
     * Checks if a file is blacklisted
     *
     * @param   {String}  filePath   File name
     * @param   {Array}   blacklist  Blacklisted items
     * @return  {Boolean}            True if the file is blacklisted
     */

    isBlacklistedFile( filePath, blacklist ) {

        return blacklist.includes( path.basename( filePath ) )

    },


    /**
     * Makes a path absolute
     *
     * @param   {String}  objPath  Object path
     * @return  {String}           Absolute path
     */

    absolutePath( objPath ) {

        if ( path.isAbsolute( objPath ) ) {
            return objPath
        }

        return path.join( __dirname, '..', objPath )

    },


    /**
     * Join two paths
     *
     * @param   {String}  path1  First path
     * @param   {String}  path2  Second path
     * @return  {String}         Joined paths
     */

    joinPaths( path1, path2 ) {

        return path.join( path1, path2 )

    },


    /**
     * Get the file name with(out) extention
     *
     * @param   {String}  filePath  File path
     * @param   {String}  fileExt   File extention
     * @return  {String}            File/Base name
     */

    fileName( filePath, fileExt ) {

        return path.basename( filePath, fileExt )

    }
}
