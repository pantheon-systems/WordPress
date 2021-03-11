#!/usr/bin/env node


if ( ! require( 'semver' ).satisfies(
    process.versions.node,
    require( './package.json' ).engines.node
) ) {
    console.error( 'Incorrect Node.js version' )
    process.exit( 1 )
}


require( './lib/app' ).wpcheck(
    require( 'minimist' )(
        process.argv.slice( 2 ),
        require( './config/minimist.json' )
    )
)
