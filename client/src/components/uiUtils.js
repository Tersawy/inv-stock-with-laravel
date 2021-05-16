import { BToast } from "bootstrap-vue";

class uiUtils {
	showToast(message, variant) {
		let bootStrapToaster = new BToast();

		bootStrapToaster.$bvToast.toast(message, {
			title: message,
			toaster: "b-toaster-top-right",
			solid: true,
			appendToast: false,
			bodyClass: "d-none",
			variant
		});
	}
}

export default new uiUtils();
