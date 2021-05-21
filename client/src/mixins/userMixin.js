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
		}
	}
};

export default userMixin;
