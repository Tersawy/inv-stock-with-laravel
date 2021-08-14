<template>
	<table class="table table-striped mb-0">
		<tbody>
			<tr>
				<td class="border-0 font-weight-400">Order Tax</td>
				<td class="border-0">$ {{ invoiceTaxFixed | floating }} ( {{ invoiceTaxPercent | floating }}% )</td>
			</tr>
			<tr>
				<td class="font-weight-400">Discount</td>
				<td>$ {{ invoiceDiscountFixed | floating }} ( {{ invoiceDiscountPercent | floating }}% )</td>
			</tr>
			<tr>
				<td class="font-weight-400">Shipping</td>
				<td>$ {{ invoiceShipping | floating }}</td>
			</tr>
			<tr>
				<td class="font-weight-600">Total Price</td>
				<td class="font-weight-600 text-primary">$ {{ invoiceTotalPrice | floating }}</td>
			</tr>
		</tbody>
	</table>
</template>

<script>
	import { DISCOUNT_FIXED, DISCOUNT_PERCENT } from "@/helpers/constants";
	export default {
		props: ["invoice", "invoiceFieldName", "net"],

		computed: {
			isPrice() {
				return this.invoiceFieldName == "price";
			},

			invoiceDiscountFixed() {
				if (!+this.invoice.discount) return 0;

				let isFixed = this.invoice.discount_method == DISCOUNT_FIXED;

				if (isFixed) return this.invoice.discount;

				let dicountFixed = this.invoice.discount * (this.totalPriceOfSubtotal / 100);

				return dicountFixed;
			},

			invoiceDiscountPercent() {
				if (!+this.invoice.discount) return 0;

				let isPercent = this.invoice.discount_method == DISCOUNT_PERCENT;

				if (isPercent) return this.invoice.discount;

				let discountPercent = this.invoice.discount / (this.totalPriceOfSubtotal / 100);

				return discountPercent;
			},

			invoiceTaxPercent() {
				if (!+this.invoice.tax) return 0;

				return this.invoice.tax;
			},

			invoiceTaxFixed() {
				if (!+this.invoice.tax) return 0;

				let totalPriceWithoutDiscount = this.totalPriceOfSubtotal - this.invoiceDiscountFixed;

				let invoiceTax = this.invoice.tax * (totalPriceWithoutDiscount / 100);

				return invoiceTax;
			},

			invoiceShipping() {
				if (!+this.invoice.shipping) return 0;

				return this.invoice.shipping;
			},

			totalPriceOfSubtotal() {
				let getTotal = (t, p) => {
					t += this.isPrice ? this.net(p).subtotalPrice : this.net(p).subtotalCost;
					return t;
				};

				return this.invoice.products.reduce(getTotal, 0);
			},

			invoiceTotalPrice() {
				let total = this.totalPriceOfSubtotal - this.invoiceDiscountFixed + this.invoiceShipping + this.invoiceTaxFixed;

				this.invoice.total_price = total;

				return total;
			}
		}
	};
</script>
