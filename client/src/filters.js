import Vue from "vue";
import { PAYMENT_METHODS_STR } from "@/helpers/constants";

Vue.filter("relation", function (value, key) {
	if (!value) return "Unknown";

	if (typeof value === "string") return value;

	if (typeof value === "object") {
		if (typeof key !== "undefined" && value[key] !== "undefined") {
			return value[key];
		} else {
			return value.name;
		}
	}
});

Vue.filter("floating", function (value, num = 2) {
	value = value ? value : 0;

	let [f, l] = value.toString().split(".");

	if (!l) return `${f}.${Array(num).fill(0).join("")}`;

	if (l.length < num) {
		return `${f}.${l.slice(0, num)}${Array(num - l.length)
			.fill(0)
			.join("")}`;
	}

	return `${f}.${l.slice(0, num)}`;
});

Vue.filter("paymentMethod", (value) => PAYMENT_METHODS_STR[value]);
