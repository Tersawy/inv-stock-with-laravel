module.exports = {
	root: true,
	env: {
		node: true
	},
	extends: ["plugin:vue/base", "eslint:recommended", "@vue/prettier"],
	parser: "vue-eslint-parser",
	parserOptions: {
		parser: "babel-eslint",
		ecmaVersion: 7,
		sourceType: "module",
		vueFeatures: {
			filter: true,
			interpolationAsNonHTML: false
		}
	},
	rules: {
		// "no-console":0,
		"no-console": process.env.NODE_ENV === "production" ? "warn" : "off",
		"no-debugger": process.env.NODE_ENV === "production" ? "warn" : "off",
		quotes: [2, "double", "avoid-escape"],
		"comma-dangle": ["error", "never"]
	},
	plugins: ["vue"]
};
