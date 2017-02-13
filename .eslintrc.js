module.exports = {
	"env": {
		"browser": true,
		"es6": true
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
		]
	},
	"globals": {
		"pg": true // ProntoGioco
	}
};