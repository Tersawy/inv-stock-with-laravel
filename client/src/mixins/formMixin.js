const formMixin = {
	computed: {
		isUpdate() {
			return this.oldItem && Object.keys(this.oldItem).length > 1;
		},

		modalVariant() {
			return this.isUpdate ? this.updateVariant || "success" : this.createVariant || "primary";
		}
	},
	methods: {
		resetForm(varDataName) {
			for (let k in varDataName) {
				varDataName[k] = null;
			}

			this.removeErrors();
		},

		finished(callback) {
			let isCallback = typeof callback === "function";

			if (!Object.keys(this.errors).length) {
				this.$emit("finished");
				this.$bvModal.hide(this.modalId);
				isCallback ? callback() : null;
			}
		},

		modalClosed(varDataName) {
			this.$emit("closed");
			if (varDataName) this.resetForm(varDataName);
		}
	}
};

export default formMixin;
