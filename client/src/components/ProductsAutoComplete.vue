<template>
	<Autocomplete
		:search="autoSearch"
		aria-label="Search in Products by code or name"
		placeholder="Search in Products by code or name"
		@submit="selectProduct"
		ref="autocomplete"
	>
		<template #result="{ result, props }">
			<li v-bind="props" class="d-flex">
				<b-avatar :src="APP_PRODUCTS_URL + result.image"></b-avatar>
				<div class="mx-3">
					<h6 class="text-muted">{{ result.name }} - {{ result.code }}</h6>
					<span class="text-muted" v-if="result.variant">{{ result.variant }}</span>
				</div>
			</li>
		</template>
	</Autocomplete>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	import { DISCOUNT_FIXED } from "@/helpers/constants";
	import Autocomplete from "@trevoreyre/autocomplete-vue";
	import "@trevoreyre/autocomplete-vue/dist/style.css";
	export default {
		props: ["invoice", "invoiceFieldName"],

		components: { Autocomplete },

		computed: {
			...mapState({
				productsOpt: (state) => state.Product.options,
				productDetails: (state) => state.Product.details
			}),

			isPrice() {
				return this.invoiceFieldName == "price";
			},

			APP_PRODUCTS_URL() {
				return process.env.VUE_APP_BASE_URL + "images/products/";
			}
		},

		methods: {
			...mapActions({
				getProductDetails: "Product/details"
			}),

			autoSearch(input) {
				if (!input || !input.toString().length) {
					return [];
				}

				let selected = this.invoice.products.map((product) => ({ id: product.id, variant_id: product.variant_id }));

				let selectedIds = selected.map((s) => s.id);

				let optionsWithoutSelected = this.productsOpt.filter((opt) => {
					if (selectedIds.includes(opt.id)) {
						let oldSelected = selected.find((s) => s.variant_id == opt.variant_id);

						if (oldSelected) return false;
					}

					let hasText = opt.name.toLocaleLowerCase().indexOf(input.toLocaleLowerCase()) > -1;
					let hasCode = opt.code && opt.code.indexOf(input) > -1;

					return hasText || hasCode;
				});

				return optionsWithoutSelected;
			},

			async selectProduct(product) {
				this.$refs.autocomplete.value = "";

				if (!product) return;

				if (!this.invoice.warehouse_id) return this.setError("Please choose the warehouse first");

				await this.getProductDetails({ id: product.id, warehouse_id: this.invoice.warehouse_id });

				if (this.productDetails && Object.keys(this.productDetails).length > 1) {
					let productWithDetails = {
						quantity: 1,
						discount: 0,
						discount_method: DISCOUNT_FIXED,
						...product,
						...this.productDetails
					};

					this.productDetails.subtotal = this.isPrice ? this.productDetails.total_price : this.productDetails.total_cost;

					this.invoice.products = [...this.invoice.products, productWithDetails];
				}
			}
		}
	};
</script>
