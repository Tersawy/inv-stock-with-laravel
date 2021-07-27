import Vue from "vue";
import App from "@/App.vue";
import router from "@/router";

import "@fortawesome/fontawesome-free/js/all";
import "@fortawesome/fontawesome-free/css/all.css";

import { BootstrapVue, BootstrapVueIcons } from "bootstrap-vue";
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);

import i18n from "./i18n";

import "@/assets/scss/main.scss";

import store from "@/store";
Vue.prototype.$store = store;

import $axios from "@/plugins/api";
Vue.prototype.$axios = $axios;

import EditIcon from "@/components/ui/EditIcon.vue";
Vue.component("EditIcon", EditIcon);

import DeleteIcon from "@/components/ui/DeleteIcon.vue";
Vue.component("DeleteIcon", DeleteIcon);

import { runGlobalMixins } from "@/mixins";
runGlobalMixins();

Vue.config.productionTip = false;

router.beforeEach(async (to, from, next) => {
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

new Vue({
	store,
	router,
	i18n,
	render: (h) => h(App)
}).$mount("#app");
