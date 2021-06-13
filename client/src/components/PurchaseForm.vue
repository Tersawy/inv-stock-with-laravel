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
					<!-- <b-col>
						<b-form-group label="Discount Type" label-for="discount_method">
							<b-select id="discount_method" v-model.number="purchase.discount_method" :options="discountMethods" />
							<InputError field="discount_method" />
						</b-form-group>
					</b-col> -->
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
						<b-form-group label="Product" label-for="product">
							<b-form-select id="product" v-model="product">
								<template #first>
									<b-form-select-option :value="null" disabled>-- Please select an product --</b-form-select-option>
								</template>
								<b-form-select-option v-for="(product, i) in productsOpt" :key="i" :value="product.id">
									<b-badge variant="primary"> ( {{ product.name }} ) </b-badge>
									<span v-if="product.variant">{{ product.variant }}-</span>{{ product.code }}
								</b-form-select-option>
							</b-form-select>
							<!-- <b-form-input list="product" />
							<datalist id="product">
								<option v-for="(product, i) in productsOpt" :key="i">
									<div v-if="!product.variant">
										{{ product.code }} -
										<b-badge variant="primary"> {{ product.name }} </b-badge>
									</div>
									<div v-else></div>
								</option>
							</datalist> -->
							<InputError field="note" />
						</b-form-group>
					</b-col>
				</b-row>
				<b-btn :variant="`${isUpdate ? 'success' : 'primary'}`" type="submit">save</b-btn>
			</b-form>
		</b-container>
	</div>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	import { formMixin } from "@/mixins";
	import { DISCOUNT_FIXED, PURCHASE_STATUS_RECEIVED, DISCOUNT_PERCENT } from "@/helpers/constants";
	import InputError from "@/components/ui/InputError.vue";
	import DefaultInput from "@/components/ui/DefaultInput.vue";
	export default {
		components: { DefaultInput, InputError },

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
				product: null
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
				purchaseStatus: (state) => state.purchaseStatus
			}),

			isUpdate() {
				return this.$route.params.purchaseId;
			},

			DISCOUNT_FIXED() {
				return DISCOUNT_FIXED;
			}
		},

		methods: {
			...mapActions({
				create: "Purchase/create",
				update: "Purchase/update",
				getProductsOpt: "Product/options",
				getSuppliersOpt: "Supplier/options",
				getWarehousesOpt: "Warehouse/options",
				getPurchase: "Purchase/one"
			}),

			changeDiscountMethod() {
				this.purchase.discount_method = this.purchase.discount_method == DISCOUNT_FIXED ? DISCOUNT_PERCENT : DISCOUNT_FIXED;
			},

			handleSave() {
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
</script>
