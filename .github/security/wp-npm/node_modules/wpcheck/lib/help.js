
/**
 * Required modules
 */
const config = require( '../config/help.json' )


console.log(
    config.format,
    config.name.join("\n\t"),
    config.usage,
    config.options.join("\n\t")
)
