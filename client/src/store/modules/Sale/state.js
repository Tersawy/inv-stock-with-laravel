import { SALE_COMPLETED, SALE_PENDING, SALE_ORDERED } from "@/helpers/constants";

export default {
	prefix: "sale",
	statusOptions: [
		{ text: "Completed", value: SALE_COMPLETED },
		{ text: "Pending", value: SALE_PENDING },
		{ text: "Ordered", value: SALE_ORDERED }
	]
};
