<template>
	<div class="purchase-form py-3">
		<b-container fluid>
			<b-form @submit.prevent="handleSave">
				<b-row>
					<b-col cols="9">
						<b-card>
							<b-row cols="3">
								<!-- -------------Date------------- -->
								<b-col>
									<date-input :object="invoice" />
									<input-error :vuelidate="this.$v.invoice.date" field="date" :namespace="namespace" />
								</b-col>

								<!-- -------------Warehouse------------- -->
								<b-col>
									<warehouse-input :object="invoice" />
									<input-error :vuelidate="this.$v.invoice.warehouse_id" field="warehouse_id" :namespace="namespace" />
								</b-col>

								<!-- -------------Supplier------------- -->
								<b-col>
									<supplier-input :object="invoice" />
									<input-error :vuelidate="this.$v.invoice.supplier_id" field="supplier_id" :namespace="namespace" />
								</b-col>
							</b-row>
						</b-card>

						<!-- -------------Product Search------------- -->
						<b-card class="my-30px">
							<products-auto-complete :invoice="invoice" :invoiceFieldName="invoiceFieldName" />
							<input-error :vuelidate="this.$v.invoice.products" field="products" :namespace="namespace" />
						</b-card>

						<!-- -------------Products Table------------- -->
						<b-card>
							<invoice-details-table :invoice="invoice" :invoiceFieldName="invoiceFieldName" :namespace="namespace" :net="net" />
						</b-card>

						<!-- -------------Note------------- -->
						<b-card class="mt-30px">
							<note-input :object="invoice" />
							<input-error :vuelidate="this.$v.invoice.note" field="note" :namespace="namespace" />
							<div class="text-right">
								<b-btn :variant="`${invoiceIdParam ? 'success' : 'primary'}`" type="submit">save</b-btn>
							</div>
						</b-card>
					</b-col>

					<b-col cols="3">
						<b-card>
							<b-row cols="1">
								<!-- -------------Shipping Cost------------- -->
								<b-col>
									<shipping-input :object="invoice" />
									<input-error :vuelidate="this.$v.invoice.shipping" field="shipping" :namespace="namespace" />
								</b-col>

								<!-- -------------Order Tax------------- -->
								<b-col>
									<tax-input :object="invoice" />
									<input-error :vuelidate="this.$v.invoice.tax" field="tax" :namespace="namespace" />
								</b-col>

								<!-- -------------Discount------------- -->
								<b-col>
									<discount-input :object="invoice" />
									<input-error :vuelidate="this.$v.invoice.discount" field="discount" :namespace="namespace" />
								</b-col>

								<!-- -------------Status------------- -->
								<b-col>
									<invoice-status-input :invoice="invoice" :namespace="namespace" />
									<input-error :vuelidate="this.$v.invoice.status" field="status" :namespace="namespace" />
								</b-col>
							</b-row>
						</b-card>

						<!-- -------------Invoice Details------------- -->
						<b-card class="mt-30px">
							<invoice-details :invoice="invoice" :invoiceFieldName="invoiceFieldName" :net="net" />
						</b-card>
					</b-col>
				</b-row>
			</b-form>
		</b-container>
	</div>
</template>

<script>
	import { invoiceMixin } from "@/mixins";
	export default {
		mixins: [invoiceMixin],

		data() {
			return { namespace: "Purchase", invoiceFieldName: "cost" };
		}
	};
	/*
			<!-- <v-data-table :headers="headers" :items="selectedProducts" class="elevation-1" disable-sort hide-default-footer>
				<template #[`item.name`]="data">
					<span>{{ data.item.name }}&nbsp;</span>
				</template>
				<template #[`item.net_cost`]="data">
					<div>{{ netUnitCost(data.item).toFixed(2) }}&nbsp;$</div>
				</template>
				<template #[`item.quantity`]="data">
					<div class="d-flex align-center">
						<v-btn @click="data.item.quantity > 1 && data.item.quantity--" icon x-small>
							<v-icon color="green"> mdi-minus </v-icon>
						</v-btn>
						<span class="mx-2">
							{{ data.item.quantity }}
						</span> -->
			<!-- //<v-text-field v-model.number="data.item.quantity" :value="data.item.quantity"> </v-text-field> -->
			<!-- <v-btn @click="data.item.quantity++" icon x-small>
							<v-icon color="red"> mdi-plus </v-icon>
						</v-btn>
					</div> -->
			<!-- //<div>{{ netUnitCost(data.item).toFixed(2) }}&nbsp;$</div> -->
			<!-- </template>
				<template #[`item.discount`]="data">
					<div>{{ discount(data.item).toFixed(2) }}&nbsp;$</div>
				</template>
				<template #[`item.tax`]="data">
					<div>{{ taxCost(data.item).toFixed(2) }}&nbsp;$</div>
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
			</v-data-table> -->
			<!-- <v-col cols="4" class="mx-auto mt-5">
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
			</v-col> -->
			*/
	/*
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
					status: PURCHASE_RECEIVED,
					shipping: 0,
					note: null,
					date: today,
					products: []
				},
				productDetail: null,
				selectedProducts: [],
				productsFields: [
					{ key: "name", label: "Product Name" },
					{ key: "net_cost", label: "Net Unit Cost" },
					{ key: "instock", label: "Instock" },
					{ key: "quantity", label: "Quantity" },
					{ key: "discount", label: "Discount" },
					{ key: "tax", label: "Tax" },
					{ key: "total_cost", label: "Total Price" },
					{ key: "actions", label: "Actions" }
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

			net(product) {
				let { unit_cost, unit_price, tax, tax_method, discount = 0, discount_method = DISCOUNT_FIXED, quantity = 1 } = product;

				let netCost, netPrice, taxCost, taxPrice, discountCost, discountPrice, totalCost, totalPrice, subtotalCost, subtotalPrice;

				discountCost = discount_method == DISCOUNT_FIXED ? discount : discount * (unit_cost / 100);
				discountPrice = discount_method == DISCOUNT_FIXED ? discount : discount * (unit_price / 100);

				let costExcludingDiscount = unit_cost - discountCost,
					priceExcludingDiscount = unit_price - discountPrice;

				if (tax_method == TAX_INCLUSIVE) {
					let taxDivide = 1 + tax / 100;

					netCost = costExcludingDiscount / taxDivide;

					netPrice = priceExcludingDiscount / taxDivide;

					taxCost = costExcludingDiscount - netCost;

					taxPrice = priceExcludingDiscount - netPrice;

					// return { cost: netCost, price: netPrice, taxCost, taxPrice, discountCost, discountPrice };
				} else {
					netCost = costExcludingDiscount;

					netPrice = priceExcludingDiscount;

					taxCost = tax * (costExcludingDiscount / 100);

					taxPrice = tax * (priceExcludingDiscount / 100);
				}

				totalCost = netCost + taxCost;
				totalPrice = netPrice + taxPrice;

				subtotalCost = quantity * totalCost;
				subtotalPrice = quantity * totalPrice;

				return {
					cost: netCost,
					price: netPrice,
					taxCost,
					taxPrice,
					discountCost,
					discountPrice,
					totalCost,
					totalPrice,
					subtotalCost,
					subtotalPrice
				};
			},

			subtotal(product) {
				let net = this.net(product);

				return product.quantity * (net.cost + net.taxCost);
			},

			autoSearch(input) {
				if (!input || !input.toString().length) {
					return [];
				}

				let selected = this.selectedProducts.map((product) => ({ id: product.id, variant_id: product.variant_id }));

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

				if (!this.purchase.warehouse_id) return this.setError("Please choose the warehouse first");

				await this.getProductDetails({ id: product.id, warehouse_id: this.purchase.warehouse_id });

				if (this.productDetails && Object.keys(this.productDetails).length > 1) {
					let productWithDetails = {
						quantity: 1,
						discount: 0,
						discount_method: DISCOUNT_FIXED,
						...product,
						...this.productDetails
					};

					this.productDetails.subtotal = this.productDetails.total_cost;

					this.selectedProducts = [...this.selectedProducts, productWithDetails];
				}
			},

			incrementQuantity(row) {
				if (/\d+/.test(row.item.quantity)) {
					row.item.quantity += 1;
					row.value += 1;
				} else {
					row.item.quantity = 1;
					row.value = 1;
				}
			},

			decrementQuantity(row) {
				if (/\d+/.test(row.item.quantity) && row.item.quantity && row.item.quantity - 1 > 0) {
					row.item.quantity -= 1;
					row.value -= 1;
				} else {
					row.item.quantity = 1;
					row.value = 1;
				}
			},

			quantityPress(evt) {
				var theEvent = evt || window.event;

				// Handle paste
				if (theEvent.type === "paste") {
					var key = window.event.clipboardData.getData("text/plain");
				} else {
					// Handle key press
					var key = theEvent.keyCode || theEvent.which;
					key = String.fromCharCode(key);
				}
				var regex = /[0-9]|\./;
				if (!regex.test(key)) {
					theEvent.returnValue = false;
					if (theEvent.preventDefault) theEvent.preventDefault();
				}
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

			updateProduct(product) {
				this.selectedProducts = this.selectedProducts.map((ps) => {
					if (ps.id == product.id && ps.variant_id == product.variant_id) {
						ps.unit_cost = product.unit_cost;
						ps.unit_price = product.unit_price;
						ps.discount = product.discount;
						ps.discount_method = product.discount_method;
						ps.tax = product.tax;
						ps.tax_method = product.tax_method;

						let net = this.net(product);

						ps.net_cost = net.cost;
						ps.net_price = net.price;
						ps.tax_cost = net.taxCost;
						ps.tax_price = net.taxPrice;
						ps.total_cost = net.totalCost;
						ps.total_price = net.totalPrice;
						ps.subtotal = net.subtotalCost;
					}

					return ps;
				});
			},

			removeProduct(row) {
				this.selectedProducts = this.selectedProducts.filter((_p, i) => i != row.index);
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
		} */

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
