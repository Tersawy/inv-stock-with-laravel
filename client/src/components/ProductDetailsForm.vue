<template>
	<b-modal id="productDetailModal" @hidden="$emit('reset-modal')" hide-footer @ok="handleSave">
		<template #modal-header="{ close }">
			<div class="d-flex align-items-center justify-content-between w-100">
				<div class="d-flex align-items-center">
					<b-avatar :src="APP_PRODUCTS_URL + product.image"></b-avatar>
					<span class="m-2">{{ product.name }}</span>
				</div>
				<b-button size="sm" variant="outline-danger" @click="close()"> Close </b-button>
			</div>
		</template>
		<template #default="{ ok }">
			<b-form @submit.prevent="handleSave" v-if="product">
				<!-- -------------Product Unit Price------------- -->
				<div v-if="isPrice">
					<price-input :object="product" />
					<input-error :vuelidate="$v.product.unit_price" field="unit_price" :namespace="namespace" />
				</div>

				<!-- -------------Product Unit Cost------------- -->
				<div v-else>
					<cost-input :object="product" />
					<input-error :vuelidate="$v.product.unit_cost" field="unit_cost" :namespace="namespace" />
				</div>

				<!-- -------------Order Tax------------- -->
				<tax-input :object="product" />
				<input-error :vuelidate="$v.product.tax" field="tax" :namespace="namespace" />

				<!-- -------------Tax Method------------- -->
				<tax-methods-input :object="product" />
				<input-error :vuelidate="$v.product.tax_method" field="tax_method" :namespace="namespace" />

				<!-- -------------Discount------------- -->
				<discount-input :object="product" />
				<input-error :vuelidate="$v.product.discount" field="tax" :namespace="namespace" />

				<div class="text-right">
					<b-btn @click="ok()" variant="outline-primary">Done</b-btn>
				</div>
			</b-form>
		</template>
	</b-modal>
</template>

<script>
	import DiscountInput from "./ui/inputs/DiscountInput.vue";
	import TaxMethodsInput from "./ui/inputs/TaxMethodsInput.vue";
	import TaxInput from "./ui/inputs/TaxInput.vue";
	import CostInput from "./ui/inputs/CostInput.vue";
	import PriceInput from "./ui/inputs/PriceInput.vue";

	import { required, numeric, minValue } from "vuelidate/lib/validators";

	export default {
		components: { DiscountInput, TaxMethodsInput, TaxInput, CostInput, PriceInput },

		props: ["namespace", "product", "invoiceFieldName"],

		validations() {
			let product = {
				tax: { numeric, minValue: minValue(0) },
				tax_method: { numeric, minValue: minValue(0) },
				discount: { numeric, minValue: minValue(0) },
				discount_method: { numeric, minValue: minValue(0) }
			};

			if (this.isPrice) {
				product.unit_price = { required, numeric, minValue: minValue(1) };
			} else {
				product.unit_cost = { required, numeric, minValue: minValue(1) };
			}

			return { product };
		},

		computed: {
			isPrice() {
				return this.invoiceFieldName == "price";
			}
		},

		methods: {
			handleSave() {
				this.$emit("done", this.product);
			}
		}
	};
</script>
