import { BToast } from "bootstrap-vue";

class uiUtils {
	showToast(message, title) {
		let bootStrapToaster = new BToast();

		bootStrapToaster.$bvToast.toast(message, {
			title: title,
			toaster: "b-toaster-top-right",
			solid: true,
			appendToast: false
		});
	}
}

export default new uiUtils();
