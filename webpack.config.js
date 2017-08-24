var webpack = require('webpack')

module.exports = function(env) {
    return {
    	entry: __dirname + "/public/js/app.js",
        output: {
            path: __dirname + "/public/js/dist",
            filename: "bundle.js"
        },
    }
}