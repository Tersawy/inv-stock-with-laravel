<template>
	<div class="purchase-form mt-5">
		<b-container fluid>
			<b-form @submit.prevent="handleSave">
				<b-row cols="3">
					<b-col>
						<b-form-group label="Date" label-for="date">
							<b-form-datepicker id="date" locale="en" v-model="purchase.date" />
							<InputError field="date" />
						</b-form-group>
					</b-col>
					<b-col>
						<b-form-group label="Warehouse" label-for="warehouse_id">
							<b-select id="warehouse_id" v-model.number="purchase.warehouse_id" :options="warehousesOpt" />
							<InputError field="warehouse_id" />
						</b-form-group>
					</b-col>
					<b-col>
						<b-form-group label="Supplier" label-for="supplier_id">
							<b-select id="supplier_id" v-model.number="purchase.supplier_id" :options="suppliersOpt" />
							<InputError field="supplier_id" />
						</b-form-group>
					</b-col>
					<b-col>
						<b-form-group label="Shipping cost" label-for="shipping">
							<b-input-group append="$">
								<b-form-input type="number" min="0" id="shipping" placeholder="shipping" v-model.number="purchase.shipping" />
							</b-input-group>
						</b-form-group>
					</b-col>
					<b-col>
						<b-form-group label="Order Tax" label-for="tax">
							<b-input-group append="%">
								<b-form-input type="number" min="0" id="tax" placeholder="tax" v-model.number="purchase.tax" />
							</b-input-group>
						</b-form-group>
					</b-col>
					<b-col>
						<b-form-group label="Discount" label-for="discount">
							<b-input-group>
								<b-form-input type="number" min="0" id="discount" placeholder="discount" v-model.number="purchase.discount" />
								<b-input-group-append>
									<b-button variant="outline-primary" @click="changeDiscountMethod">
										<span class="h6 mb-0" v-if="purchase.discount_method === DISCOUNT_FIXED">$</span>
										<span class="h6 mb-0" v-else>%</span>
									</b-button>
								</b-input-group-append>
							</b-input-group>
						</b-form-group>
					</b-col>
					<b-col>
						<b-form-group label="Status" label-for="status">
							<b-select id="status" v-model.number="purchase.status" :options="purchaseStatus" />
							<InputError field="status" />
						</b-form-group>
					</b-col>
					<b-col>
						<b-form-group label="Note" label-for="note">
							<b-form-textarea id="note" v-model="purchase.note" placeholder="Enter something..." size="sm" />
							<InputError field="note" />
						</b-form-group>
					</b-col>
					<b-col>
						<v-autocomplete
							v-model="productField"
							:items="productsFields"
							:search-input.sync="search"
							:filter="filterProducts"
							@change="ProductChange"
							item-text="name"
							item-value="index"
							return-object
							hide-no-data
							hide-selected
							color="blue-grey lighten-2"
							placeholder="Search by Code or Name"
							class="p-0"
							outlined
							append-icon=""
						>
							<template #item="data">
								<template v-if="!data.item.variant">
									<v-list-item-avatar>
										<img :src="APP_PRODUCTS_URL + data.item.image" />
									</v-list-item-avatar>
									<v-list-item-content>
										<div class="flex">
											<span>{{ data.item.code }}&nbsp;&nbsp;</span>
											<v-badge :content="data.item.name"></v-badge>
										</div>
									</v-list-item-content>
								</template>
								<template v-else>
									<v-list-item-avatar>
										<img :src="APP_PRODUCTS_URL + data.item.image" />
									</v-list-item-avatar>
									<v-list-item-content>
										<v-list-item-content>
											<div class="flex">
												<span>{{ data.item.code }}&nbsp;&nbsp;</span>
												<v-badge :content="data.item.name"></v-badge>
											</div>
										</v-list-item-content>
										<v-list-item-subtitle v-text="data.item.variant"></v-list-item-subtitle>
									</v-list-item-content>
								</template>
							</template>
						</v-autocomplete>
					</b-col>
				</b-row>
				<b-btn :variant="`${isUpdate ? 'success' : 'primary'}`" type="submit">save</b-btn>
			</b-form>
		</b-container>
		<v-data-table :headers="headers" :items="selectedProducts" class="elevation-1" disable-sort hide-default-footer>
			<template #[`item.name`]="data">
				<span>{{ data.item.name }}&nbsp;</span>
			</template>
			<template #[`item.net_cost`]="data">
				<div>{{ netUnitCost(data.item).toFixed(2) }}&nbsp;$</div>
			</template>
			<template #[`item.quantity`]="data">
				<!-- <v-input
					hide-details
					append-icon="mdi-minus"
					prepend-icon="mdi-plus"
					@click:append="data.item.quantity > 1 && data.item.quantity--"
					@click:prepend="data.item.quantity++"
				> -->
				<!-- {{ data.item.quantity }} -->
				<!-- <v-text-field hide-details v-model.number="data.item.quantity" class="pt-0 mt-0"> </v-text-field> -->
				<!-- </v-input> -->
				<div class="d-flex align-center">
					<v-btn @click="data.item.quantity > 1 && data.item.quantity--" icon x-small>
						<v-icon color="green"> mdi-minus </v-icon>
					</v-btn>
					<span class="mx-2">
						{{ data.item.quantity }}
					</span>
					<!-- <v-text-field v-model.number="data.item.quantity" :value="data.item.quantity"> </v-text-field> -->
					<v-btn @click="data.item.quantity++" icon x-small>
						<v-icon color="red"> mdi-plus </v-icon>
					</v-btn>
				</div>
				<!-- <div>{{ netUnitCost(data.item).toFixed(2) }}&nbsp;$</div> -->
			</template>
			<template #[`item.discount`]="data">
				<div>{{ discount(data.item).toFixed(2) }}&nbsp;$</div>
			</template>
			<template #[`item.tax`]="data">
				<div>{{ taxAmount(data.item).toFixed(2) }}&nbsp;$</div>
			</template>
			<template #[`item.subtotal`]="data">
				<div>{{ subtotal(data.item).toFixed(2) }}&nbsp;$</div>
			</template>
			<template #[`item.actions`]="data">
				<div class="d-flex">
					<v-btn @click="updateProduct(data.item)" color="green" x-small dark fab elevation="1" class="me-3">
						<v-icon>mdi-square-edit-outline </v-icon>
					</v-btn>
					<v-btn @click="removeProduct(data.item)" color="red" x-small dark fab elevation="1">
						<v-icon>mdi-close</v-icon>
					</v-btn>
				</div>
			</template>
		</v-data-table>
		<v-col cols="4" class="mx-auto mt-5">
			<v-simple-table dense>
				<template v-slot:default>
					<tbody>
						<tr class="grey lighten-3">
							<td>Order Tax</td>
							<td>{{ orderTaxFixed.toFixed(2) }} $&nbsp;&nbsp;({{ orderTaxPercent.toFixed(2) }} %)</td>
						</tr>
						<tr class="grey lighten-4">
							<td>Discount</td>
							<td>{{ orderDiscountFixed.toFixed(2) }} $&nbsp;&nbsp;({{ orderDiscountPercent.toFixed(2) }} %)</td>
						</tr>
						<tr class="grey lighten-3">
							<td>Shipping</td>
							<td>{{ orderShipping.toFixed(2) }} $</td>
						</tr>
						<tr class="grey lighten-4">
							<td>Total Price</td>
							<td>{{ orderTotalPrice.toFixed(2) }} $</td>
						</tr>
					</tbody>
				</template>
			</v-simple-table>
		</v-col>
		<ProductDetailsForm :status="dialog" @closed="detailsModalClosed" :product="product" @done="handleUpdateProduct" />
	</div>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	import { formMixin } from "@/mixins";
	import { DISCOUNT_FIXED, PURCHASE_STATUS_RECEIVED, DISCOUNT_PERCENT, TAX_INCLUSIVE } from "@/helpers/constants";
	import InputError from "@/components/ui/InputError.vue";
	import DefaultInput from "@/components/ui/DefaultInput.vue";
	import ProductDetailsForm from "@/components/ProductDetailsForm.vue";
	export default {
		components: { DefaultInput, InputError, ProductDetailsForm },

		mixins: [formMixin],

		data() {
			let zeroFill = (v) => (+v < 10 ? `0${v}` : v);

			let [m, d, y] = new Date().toLocaleDateString().split("/");

			const today = `${y}-${zeroFill(m)}-${zeroFill(d)}`;

			return {
				purchase: {
					warehouse_id: null,
					supplier_id: null,
					tax: 0,
					discount: 0,
					discount_method: DISCOUNT_FIXED,
					status: PURCHASE_STATUS_RECEIVED,
					shipping: 0,
					note: null,
					date: today,
					products: []
				},
				selectedProducts: [],
				productsFields: [],
				productField: null,
				disabled: false,
				search: null,
				product: null,
				dialog: false,
				headers: [
					{ text: "Product", value: "name" },
					{ text: "Net Unit Cost", value: "net_cost" },
					{ text: "Stock", value: "instock" },
					{ text: "Quantity", value: "quantity" },
					{ text: "Discount", value: "discount" },
					{ text: "Tax", value: "tax" },
					{ text: "Subtotal", value: "subtotal" },
					{ text: "Actions", value: "actions" }
				]
			};
		},

		async mounted() {
			this.getProductsOpt();
			this.getSuppliersOpt();
			this.getWarehousesOpt();

			if (this.isUpdate) {
				await this.getPurchase(this.$route.params.purchaseId);
				this.purchase = { ...this.oldPurchase };
			}
		},

		computed: {
			...mapState({
				warehousesOpt: (state) => state.Warehouse.options,
				suppliersOpt: (state) => state.Supplier.options,
				productsOpt: (state) => state.Product.options,
				oldPurchase: (state) => state.Purchase.one,
				discountMethods: (state) => state.discountMethods,
				taxMethods: (state) => state.taxMethods,
				purchaseStatus: (state) => state.purchaseStatus,
				productDetails: (state) => state.Product.details
			}),

			/*
					used for autocomplete component coz it doesn't duplicate items and we may have two items or more with the same id,
					so in this case we want to show all products with all variants, don't forget we return the select Obj instead of the value (id).
				*/
			productsOptForAutoComplete() {
				return this.productsOpt.map((opt, index) => ({ ...opt, index }));
			},

			fields() {
				return this.products;
			},

			isUpdate() {
				return this.$route.params.purchaseId;
			},

			DISCOUNT_FIXED() {
				return DISCOUNT_FIXED;
			},

			APP_PRODUCTS_URL() {
				return process.env.VUE_APP_BASE_URL + "images/products/";
			},

			orderDiscountFixed() {
				if (!+this.purchase.discount) return 0;

				let isFixed = this.purchase.discount_method == DISCOUNT_FIXED;

				if (isFixed) return this.purchase.discount;

				let dicountFixed = this.purchase.discount * (this.totalPriceOfSubtotal / 100);

				return dicountFixed;
			},

			orderDiscountPercent() {
				if (!+this.purchase.discount) return 0;

				let isPercent = this.purchase.discount_method == DISCOUNT_PERCENT;

				if (isPercent) return this.purchase.discount;

				let discountPercent = this.purchase.discount / (this.totalPriceOfSubtotal / 100);

				return discountPercent;
			},

			orderTaxPercent() {
				if (!+this.purchase.tax) return 0;

				return this.purchase.tax;
			},

			orderTaxFixed() {
				if (!+this.purchase.tax) return 0;

				let totalPriceWithoutDiscount = this.totalPriceOfSubtotal - this.orderDiscountFixed;

				let orderTax = this.purchase.tax * (totalPriceWithoutDiscount / 100);

				return orderTax;
			},

			orderShipping() {
				if (!+this.purchase.shipping) return 0;

				return this.purchase.shipping;
			},

			totalPriceOfSubtotal() {
				return this.selectedProducts.reduce((t, p) => {
					t += this.subtotal(p);
					return t;
				}, 0);
			},

			orderTotalPrice() {
				return this.totalPriceOfSubtotal - this.orderDiscountFixed + this.orderShipping + this.orderTaxFixed;
			}
		},

		watch: {
			search(queryText) {
				if (!queryText || !queryText.toString().length) {
					return (this.productsFields = []);
				}

				let selectedIndexs = this.selectedProducts.map((product) => product.index);

				let productsOpt = this.productsOptForAutoComplete.filter((opt) => {
					return this.filterProducts(opt, queryText, opt.name) && !selectedIndexs.includes(opt.index);
				});

				this.productsFields = productsOpt;
			}
		},

		methods: {
			...mapActions({
				create: "Purchase/create",
				update: "Purchase/update",
				getProductsOpt: "Product/options",
				getSuppliersOpt: "Supplier/options",
				getWarehousesOpt: "Warehouse/options",
				getPurchase: "Purchase/one",
				getProductDetails: "Product/details"
			}),

			netUnitCost(product) {
				return product.tax_method == TAX_INCLUSIVE
					? this.costExcludingDiscount(product) - this.taxAmount(product)
					: this.costExcludingDiscount(product);
			},

			taxAmount(product) {
				let { tax, tax_method } = product;

				if (tax_method == TAX_INCLUSIVE) {
					let taxDivide = 1 + tax / 100;
					return this.costExcludingDiscount(product) - this.costExcludingDiscount(product) / taxDivide;
				}
				return tax * (this.costExcludingDiscount(product) / 100);
			},

			discount(product) {
				let { unit_cost = 1, discount = 0, discount_method = DISCOUNT_FIXED } = product;

				return discount_method == DISCOUNT_FIXED ? discount : discount * (unit_cost / 100);
			},

			costExcludingDiscount(product) {
				return product.unit_cost - this.discount(product);
			},

			subtotal(product) {
				return product.quantity * (this.netUnitCost(product) + this.taxAmount(product));
			},

			detailsModalClosed() {
				this.product = null;
				this.dialog = false;
			},

			handleUpdateProduct(details) {
				let productSelected = this.selectedProducts.find((p) => p.index == details.index);
				for (let k in details) {
					productSelected[k] = details[k];
				}
				this.detailsModalClosed();
			},

			filterProducts(item, queryText, itemText) {
				let hasText = itemText.toLocaleLowerCase().indexOf(queryText.toLocaleLowerCase()) > -1;
				let hasCode = item.code && item.code.indexOf(queryText) > -1;
				return hasText || hasCode;
			},

			updateProduct(product) {
				this.product = product;
				this.dialog = true;
			},

			removeProduct(item) {
				const index = this.selectedProducts.findIndex((p) => p.index == item.index);
				if (index >= 0) this.selectedProducts.splice(index, 1);
				// const index = this.selectedProducts.indexOf(item);
				// if (index >= 0) this.selectedProducts.splice(index, 1);
			},

			async ProductChange(product) {
				await this.getProductDetails(product.id);

				if (this.productDetails && Object.keys(this.productDetails).length > 1) {
					let productWithDetails = {
						quantity: 1,
						discount: 0,
						discount_method: DISCOUNT_FIXED,
						...product,
						...this.productDetails
					};

					this.productDetails.subtotal = this.subtotal(productWithDetails);
					console.log(productWithDetails);
					this.selectedProducts = [...this.selectedProducts, productWithDetails];
					this.productField = {};
					this.productsFields = [];
					this.search = null;
				}
			},

			changeDiscountMethod() {
				this.purchase.discount_method = this.purchase.discount_method == DISCOUNT_FIXED ? DISCOUNT_PERCENT : DISCOUNT_FIXED;
			},

			handleSave() {
				this.purchase.products = this.selectedProducts.map((selected) => ({
					cost: selected.unit_cost,
					quantity: selected.quantity,
					tax: selected.tax,
					tax_method: selected.tax_method,
					discount: selected.discount,
					discount_method: selected.discount_method,
					product_id: selected.id,
					variant_id: selected.variant_id
				}));

				if (this.isUpdate) return this.handleUpdate();

				return this.handleCreate();
			},

			async handleCreate() {
				await this.create(this.purchase);

				this.finished();
			},

			async handleUpdate() {
				await this.update(this.purchase);
				this.finished();
			}
		}
	};
	/*
	<template #selection="data">
		<v-chip
			color="primary"
			small
			v-bind="data.attrs"
			:input-value="data.selected"
			close
			@click.stop="data.select"
			@click:close="removeProduct(data.item)"
		>
			<span v-if="data.item.variant">{{ data.item.variant }}&nbsp;-&nbsp;</span>
			{{ data.item.name }}
		</v-chip>
	</template>
	<template #item="data">
		<template v-if="!data.item.variant">
			<v-list-item-avatar>
				<img :src="APP_PRODUCTS_URL + data.item.image" />
			</v-list-item-avatar>
			<v-list-item-content>
				<div class="flex">
					<span>{{ data.item.code }}&nbsp;&nbsp;</span>
					<v-badge :content="data.item.name"></v-badge>
				</div>
			</v-list-item-content>
		</template>
		<template v-else>
			<v-list-item-avatar>
				<img :src="APP_PRODUCTS_URL + data.item.image" />
			</v-list-item-avatar>
			<v-list-item-content>
				<v-list-item-content>
					<div class="flex">
						<span>{{ data.item.code }}&nbsp;&nbsp;</span>
						<v-badge :content="data.item.name"></v-badge>
					</div>
				</v-list-item-content>
				<v-list-item-subtitle v-text="data.item.variant"></v-list-item-subtitle>
			</v-list-item-content>
		</template>
	</template>

	//////////////////////////////////////////////////////////////////////////////////

	<!-- <template #selection="data">
		<v-chip
			color="primary"
			small
			v-bind="data.attrs"
			:input-value="data.selected"
			close
			@click.stop="data.select"
			@click:close="removeProduct(data.item)"
		>
			<span v-if="data.item.variant">{{ data.item.variant }}&nbsp;-&nbsp;</span>
			{{ data.item.name }}
		</v-chip>
	</template> -->
		*/
</script>
