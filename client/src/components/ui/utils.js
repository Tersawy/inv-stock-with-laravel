import { BToast } from "bootstrap-vue";

export const showToast = (message, variant = "success") => {
	let bootStrapToaster = new BToast();

	bootStrapToaster.$bvToast.toast(message, {
		title: message,
		toaster: "b-toaster-top-right",
		solid: true,
		appendToast: false,
		bodyClass: "d-none",
		variant
	});
};
