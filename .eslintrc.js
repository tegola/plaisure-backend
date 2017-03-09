module.exports = {
	"env": {
		"browser": true,
		"es6": true
	},
	"globals": {
		"pg": true // ProntoGioco
	},
	"extends": "eslint:recommended",
	"plugins": ["vue"],
	"parserOptions": {
		"sourceType": "module"
	},
	"rules": {
		"indent": [
			"error",
			"tab"
		],
		"linebreak-style": [
			"error",
			"unix"
		],
		"no-console": "off",
		"quotes": [
			"error",
			"single"
		],
        "semi": [
            "error",
            "always"
        ]
	}
};