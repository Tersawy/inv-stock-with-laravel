import { TRANSFER_COMPLETED, TRANSFER_PENDING, TRANSFER_SENT } from "@/helpers/constants";

export default {
	prefix: "transfer",
	statusOptions: [
		{ text: "Completed", value: TRANSFER_COMPLETED },
		{ text: "Pending", value: TRANSFER_PENDING },
		{ text: "Sent", value: TRANSFER_SENT }
	]
};
