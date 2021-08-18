import Vue from "vue";
import App from "@/App.vue";
import router from "@/router";

import "@fortawesome/fontawesome-free/js/all";
import "@fortawesome/fontawesome-free/css/all.css";

import { BootstrapVue, BootstrapVueIcons } from "bootstrap-vue";
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);

import i18n from "./plugins/i18n";

import "@/assets/scss/main.scss";

import vuelidate from "vuelidate";
Vue.use(vuelidate);

import store from "@/store";
Vue.prototype.$store = store;

import $axios from "@/plugins/api";
Vue.prototype.$axios = $axios;

import EditIcon from "@/components/ui/EditIcon.vue";
Vue.component("EditIcon", EditIcon);

import DeleteIcon from "@/components/ui/DeleteIcon.vue";
Vue.component("DeleteIcon", DeleteIcon);

import InputError from "@/components/ui/InputError.vue";
Vue.component("InputError", InputError);

import { runGlobalMixins } from "@/mixins";
runGlobalMixins();

Vue.config.productionTip = false;

function breads() {
	let path = router.history.current.path;

	let paths = path.split("/");

	let realpaths = paths.filter((p) => !!p);

	realpaths = ["", ...realpaths];

	let results = realpaths.map((p, i) => {
		let isLast = i + 1 == realpaths.length,
			isFirst = i == 0,
			name = isFirst ? "dashboard" : p;

		name = name.charAt(0).toUpperCase() + name.slice(1);

		let path = isFirst ? "/" : realpaths.slice(0, i + 1).join("/");

		return { name, to: path, isLast, isFirst };
	});

	return results;
}

const DEFAULT_TITLE = "Inv-Stock";

router.afterEach((to) => {
	store.commit("setBreads", breads(to));

	Vue.nextTick(() => {
		document.title = to.meta.title || DEFAULT_TITLE;
	});
});

router.beforeEach(async (to, _from, next) => {
	let user = store.state.user;

	user = user ? user : sessionStorage.getItem("user");

	user = user ? user : {};

	user = typeof user === "object" ? user : JSON.parse(user);

	let isUser = Object.keys(user).length > 1;

	if (!isUser && to.meta.auth) return next({ name: "Login" });

	if (isUser && (!to.meta.auth || (to.meta.auth && !to.meta.userType.includes(+user.type)))) {
		if ([0, 1, 2].includes(+user.type)) return next({ name: "Dashboard" });
		if ([3].includes(+user.type)) return next({ name: "CustomerDashboard" });
	}

	// If !isUser the user will be next to Login page
	// if isUser will complete his route
	next();
});

import "./directives";
import "./filters";

new Vue({
	store,
	router,
	i18n,
	render: (h) => h(App)
}).$mount("#app");
