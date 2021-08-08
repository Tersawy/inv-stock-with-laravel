import { TAX_EXCLUSIVE, TAX_INCLUSIVE, DISCOUNT_FIXED, DISCOUNT_PERCENT } from "@/helpers/constants";

const state = {
	successMsg: "",
	successDismissSecs: 0,
	errorMsg: "",
	errorDismissSecs: 0,
	errors: {},
	breads: [],
	taxMethods: [
		{ text: "Exclusive", value: TAX_EXCLUSIVE },
		{ text: "Inclusive", value: TAX_INCLUSIVE }
	],
	discountMethods: [
		{ text: "Fixed", value: DISCOUNT_FIXED },
		{ text: "Percent %", value: DISCOUNT_PERCENT }
	]
};

export default state;
