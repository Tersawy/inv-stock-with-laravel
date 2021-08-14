import { SALE_RETURN_RECEIVED, SALE_RETURN_PENDING } from "@/helpers/constants";

export default {
	prefix: "sale-return",
	statusOptions: [
		{ text: "Received", value: SALE_RETURN_RECEIVED },
		{ text: "Pending", value: SALE_RETURN_PENDING }
	]
};
