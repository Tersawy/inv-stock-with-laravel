import { PURCHASE_RECEIVED, PURCHASE_PENDING, PURCHASE_ORDERED } from "@/helpers/constants";

export default {
	prefix: "purchase",
	statusOptions: [
		{ text: "Received", value: PURCHASE_RECEIVED },
		{ text: "Pending", value: PURCHASE_PENDING },
		{ text: "Ordered", value: PURCHASE_ORDERED }
	]
};
