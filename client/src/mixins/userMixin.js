import { mapState } from "vuex";

const userMixin = {
	computed: {
		...mapState({
			user: (state) => state.Auth.user
		}),

		isAuth() {
			return Object.keys(this.user).length > 0;
		},

		isOwner() {
			return this.user.type === 0;
		},

		isAdmin() {
			return this.user.type === 1;
		},

		isUser() {
			return this.user.type === 2;
		},

		isCustomer() {
			return this.user.type === 3;
		}
	}
};

export default userMixin;
