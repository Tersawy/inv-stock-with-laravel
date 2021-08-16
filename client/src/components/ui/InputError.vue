<template>
	<div class="input-error text-danger">
		<span v-if="errorExist">{{ errorMsg() }}</span>
	</div>
</template>

<script>
	export default {
		props: ["field", "vuelidate", "namespace"],
		computed: {
			errors() {
				return this.$store.state[this.namespace].errors;
			},

			isVuelidateError() {
				return this.vuelidate.$invalid && this.vuelidate.$dirty;
			},

			isStoreError() {
				return !!this.errors[this.field] && !!this.errors[this.field][0];
			},

			errorExist() {
				return this.isVuelidateError || this.isStoreError;
			}
		},
		methods: {
			errorMsg() {
				if (this.isStoreError) return this.errors[this.field][0];

				let error = "";

				if (this.isVuelidateError) {
					Object.keys(this.vuelidate.$params).forEach((p) => {
						if (!this.vuelidate[p]) {
							let [attr] = this.field.split("_");

							attr = attr.charAt().toUpperCase() + attr.slice(1);

							attr = attr.endsWith("s") ? attr + " are " : attr + " is ";

							let value = "";

							if (p == "minValue" || p == "minLength") {
								value = this.vuelidate.$params[p].min;
							}
							if (p == "maxValue" || p == "maxLength") {
								value = this.vuelidate.$params[p].max;
							}

							error = this.$t(`validation.${p}`, { attr, value });
						}
					});
				}

				return error;
			}
		}
	};
</script>

<style lang="scss">
	.input-error {
		font-size: 10px;
		margin-top: 3px;
		padding-bottom: 10px;
	}
	.form-group {
		margin-bottom: 0;
	}
</style>
