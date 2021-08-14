import { PURCHASE_RETURN_COMPLETED, PURCHASE_RETURN_PENDING } from "@/helpers/constants";

export default {
	prefix: "purchase-return",
	statusOptions: [
		{ text: "Completed", value: PURCHASE_RETURN_COMPLETED },
		{ text: "Pending", value: PURCHASE_RETURN_PENDING }
	]
};
