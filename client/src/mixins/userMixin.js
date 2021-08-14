import { mapState } from "vuex";

const userMixin = {
	computed: {
		...mapState({
			me: (state) => state.Auth.user
		}),

		isAuth() {
			return Object.keys(this.me).length > 0;
		},

		isOwner() {
			return this.me.type === 0;
		},

		isAdmin() {
			return this.me.type === 1;
		},

		isUser() {
			return this.me.type === 2;
		},

		isCustomer() {
			return this.me.type === 3;
		},

		APP_PRODUCTS_URL() {
			return process.env.VUE_APP_BASE_URL + "images/products/";
		},

		APP_BRANDS_URL() {
			return process.env.VUE_APP_BASE_URL + "images/brands/";
		}
	}
};

export default userMixin;
