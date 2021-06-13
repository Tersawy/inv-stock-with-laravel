import {
	TAX_EXCLUSIVE,
	TAX_INCLUSIVE,
	DISCOUNT_FIXED,
	DISCOUNT_PERCENT,
	PURCHASE_STATUS_RECEIVED,
	PURCHASE_STATUS_PENDING,
	PURCHASE_STATUS_ORDERED
} from "@/helpers/constants";

const state = {
	successMsg: "",
	successDismissSecs: 0,
	errorMsg: "",
	errorDismissSecs: 0,
	errors: {},
	taxMethods: [
		{ text: "Exclusive", value: TAX_EXCLUSIVE },
		{ text: "Inclusive", value: TAX_INCLUSIVE }
	],
	discountMethods: [
		{ text: "Fixed", value: DISCOUNT_FIXED },
		{ text: "Percent %", value: DISCOUNT_PERCENT }
	],
	purchaseStatus: [
		{ text: "Received", value: PURCHASE_STATUS_RECEIVED },
		{ text: "Pending", value: PURCHASE_STATUS_PENDING },
		{ text: "Ordered", value: PURCHASE_STATUS_ORDERED }
	]
};

export default state;
