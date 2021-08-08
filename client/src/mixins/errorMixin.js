export default {
	computed: {
		errors() {
			this.$store.state[this.namespace].errors;
		}
	},

	methods: {
		/**
			@param obj e.g. { field: "fieldName", msg: "Message" }
		*/
		setError(obj) {
			this.$store.commit(this.namespace + "/setError", obj);
		},

		/**
			@param field string
		*/
		removeError(field) {
			this.$store.commit(this.namespace + "/removeError", field);
		},

		/**
			@param obj e.g. { errors: { field: ["error msg 1", "error msg 2"] }, message: "Global Msg" }
		*/
		setErrors(obj) {
			this.$store.commit(this.namespace + "/removeError", obj);
		},

		removeErrors() {
			this.$store.commit(this.namespace + "/removeError");
		},

		/**
			@param message string
		*/
		setGlobalError(message) {
			this.$store.commit("setError", message);
		}
	}
};
