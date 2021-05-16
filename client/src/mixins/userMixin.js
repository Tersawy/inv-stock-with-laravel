import { mapState } from "vuex";

const userMixin = {
	computed: {
		...mapState({
			user: (state) => state.Auth.user
		}),

		isAuth() {
			return Object.keys(this.user).length > 0;
		}
	}
};

export default userMixin;
