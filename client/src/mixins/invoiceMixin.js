import DiscountInput from "@/components/ui/inputs/DiscountInput";
import TaxInput from "@/components/ui/inputs/TaxInput.vue";
import ShippingInput from "@/components/ui/inputs/ShippingInput.vue";
import SupplierInput from "@/components/ui/inputs/SupplierInput.vue";
import CustomerInput from "@/components/ui/inputs/CustomerInput.vue";
import WarehouseInput from "@/components/ui/inputs/WarehouseInput.vue";
import DateInput from "@/components/ui/inputs/DateInput.vue";
import NoteInput from "@/components/ui/inputs/NoteInput.vue";
import InvoiceStatusInput from "@/components/ui/inputs/InvoiceStatusInput.vue";
import InvoiceDetailsTable from "@/components/InvoiceProductsDetailsTable.vue";
import InvoiceDetails from "@/components/InvoiceDetails.vue";
import ProductsAutoComplete from "@/components/ProductsAutoComplete.vue";

import { mapActions } from "vuex";

import { DISCOUNT_FIXED, DISCOUNT_PERCENT, TAX_INCLUSIVE } from "@/helpers/constants";

import { required, numeric, minLength, maxLength, minValue } from "vuelidate/lib/validators";

export default {
	components: {
		DiscountInput,
		TaxInput,
		ShippingInput,
		SupplierInput,
		CustomerInput,
		WarehouseInput,
		DateInput,
		NoteInput,
		InvoiceStatusInput,
		InvoiceDetailsTable,
		InvoiceDetails,
		ProductsAutoComplete
	},

	data() {
		let zeroFill = (v) => (+v < 10 ? `0${v}` : v);

		let [m, d, y] = new Date().toLocaleDateString().split("/");

		const today = `${y}-${zeroFill(m)}-${zeroFill(d)}`;

		let invoice = {
			customer_id: null,
			supplier_id: null,
			warehouse_id: null,
			tax: 0,
			discount: 0,
			discount_method: DISCOUNT_FIXED,
			status: 0,
			shipping: 0,
			note: null,
			date: today,
			total_price: 0,
			products: []
		};

		return { invoice };
	},

	validations() {
		let invoice = {
			warehouse_id: { required, numeric, minValue: minValue(1) },
			tax: { numeric, minValue: minValue(0) },
			discount: { numeric, minValue: minValue(0) },
			discount_method: { numeric, minValue: minValue(0) },
			status: { numeric, minValue: minValue(0) },
			shipping: { numeric, minValue: minValue(0) },
			note: { maxLength: maxLength(255) },
			date: { required },
			products: { required, minLength: minLength(1) },
			total_price: { required, minValue: minValue(1) }
		};

		if (this.isPrice) {
			invoice.customer_id = { required, numeric, minValue: minValue(1) };
		} else {
			invoice.supplier_id = { required, numeric, minValue: minValue(1) };
		}

		return { invoice };
	},

	async mounted() {
		this.getProductsOpt();
		this.getWarehousesOpt();

		if (this.isPrice) {
			this.getCustomersOpt();
		} else {
			this.getSuppliersOpt();
		}

		if (this.invoiceIdParam) {
			await this.getInvoice(this.invoiceIdParam);
			this.invoice = { ...this.oldInvoice };
			this.invoice.products = this.invoice.products.map((product) => {
				return {
					...product,
					decrementBtn: "primary",
					incrementBtn: "primary",
					instockVariant: "outline-success"
				};
			});
		}
	},

	computed: {
		oldInvoice() {
			return this.$store.state[this.namespace].one;
		},

		isPrice() {
			return this.invoiceFieldName == "price";
		},

		invoiceIdParam() {
			return this.$route.params.invoiceId;
		},

		DISCOUNT_FIXED() {
			return DISCOUNT_FIXED;
		},

		DISCOUNT_PERCENT() {
			return DISCOUNT_PERCENT;
		},

		TAX_INCLUSIVE() {
			return TAX_INCLUSIVE;
		}
	},

	methods: {
		...mapActions({
			getWarehousesOpt: "Warehouse/options",
			getProductsOpt: "Product/options",
			getSuppliersOpt: "Supplier/options",
			getCustomersOpt: "Customer/options"
		}),

		getInvoice(invoiceId) {
			return this.$store.dispatch(`${this.namespace}/edit`, invoiceId);
		},

		create(invoice) {
			return this.$store.dispatch(`${this.namespace}/create`, invoice);
		},

		update(invoice) {
			return this.$store.dispatch(`${this.namespace}/update`, invoice);
		},

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

		async handleSave() {
			let invoice = Object.assign({}, this.invoice);

			invoice.products = invoice.products.map((selected) => {
				let product = {
					quantity: selected.quantity,
					tax: selected.tax,
					tax_method: selected.tax_method,
					discount: selected.discount,
					discount_method: selected.discount_method,
					product_id: selected.id,
					variant_id: selected.variant_id
				};

				if (this.isPrice) {
					product.price = selected.unit_price;
				} else {
					product.cost = selected.unit_cost;
				}

				return product;
			});

			this.$v.$touch();

			if (this.$v.$invalid) return;

			try {
				if (this.invoiceIdParam) return this.update(invoice);

				await this.create(invoice);

				this.$router.push({ name: this.namespace });
			} catch (err) {
				console.log(err);
			} finally {
				//
			}
		}
	}
};
