import { QUOTATION_SENT, QUOTATION_PENDING } from "@/helpers/constants";

export default {
	prefix: "quotation",
	statusOptions: [
		{ text: "Sent", value: QUOTATION_SENT },
		{ text: "Pending", value: QUOTATION_PENDING }
	]
};
