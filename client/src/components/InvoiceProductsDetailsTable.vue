<template>
	<div>
		<b-table :fields="fields" :items="invoice.products" show-empty emptyText="There are no products to show" class="mb-0" thead-tr-class="border-0">
			<template #empty="scope">
				<div class="text-center text-muted">{{ scope.emptyText }}</div>
			</template>

			<template #cell(actions)="row">
				<b-icon
					@click="productDetail = { ...row.item }"
					icon="pencil-square"
					scale="1.5"
					variant="success"
					v-b-modal.productDetailModal
					class="c-pointer mx-3 outline-0"
				></b-icon>
				<b-icon @click="removeProduct(row)" icon="trash" scale="1.5" variant="danger" class="c-pointer"></b-icon>
			</template>

			<template #cell(image)="row">
				<b-avatar :src="APP_PRODUCTS_URL + row.value" class="shadow-sm" rounded="lg"></b-avatar>
			</template>

			<template #cell(name)="row">
				<div>
					<div class="mb-2">
						<span>{{ row.value }}</span>
						<span v-if="row.item.variant_id" class="text-muted"> - {{ row.item.variant }} </span>
					</div>
					<b-badge variant="outline-info">
						{{ row.item.code }}
					</b-badge>
				</div>
			</template>

			<template #cell(net_price)="row" v-if="isPrice"> $ {{ net(row.item).price | floating }} </template>

			<template #cell(net_cost)="row" v-else> $ {{ net(row.item).cost | floating }} </template>

			<template #cell(instock)="row">
				<b-badge :variant="row.item.instockVariant"> {{ row.value | floating }} {{ row.item[isPrice ? "sale_unit" : "purchase_unit"] }} </b-badge>
			</template>

			<template #cell(quantity)="row">
				<b-input-group style="width: 110px">
					<b-input-group-prepend>
						<b-btn :variant="row.item.decrementBtn" size="sm" class="font-default" @click="decrementQuantity(row)"> - </b-btn>
					</b-input-group-prepend>
					<b-form-input
						class="form-control border-0 shadow-none bg-light text-center"
						min="1"
						@keypress="quantityPress"
						@paste="quantityPress"
						v-model.number="row.item.quantity"
						@change="quantityChanged(row)"
					/>
					<b-input-group-append>
						<b-btn :variant="row.item.incrementBtn" size="sm" class="font-default" @click="incrementQuantity(row)"> + </b-btn>
					</b-input-group-append>
				</b-input-group>
			</template>

			<template #cell(discount)="row"> $ {{ net(row.item)[isPrice ? "discountPrice" : "discountCost"] | floating }} </template>

			<template #cell(tax)="row"> $ {{ net(row.item)[isPrice ? "taxPrice" : "taxCost"] | floating }} </template>

			<template #cell(total_cost)="row">
				$ <span class="text-primary font-weight-600"> {{ net(row.item).subtotalCost | floating }} </span>
			</template>

			<template #cell(total_price)="row">
				$ <span class="text-primary font-weight-600"> {{ net(row.item).subtotalPrice | floating }} </span>
			</template>
		</b-table>

		<ProductDetailForm
			:namespace="namespace"
			:invoiceFieldName="invoiceFieldName"
			:product="productDetail"
			@done="updateProduct"
			@reset-modal="() => (productDetail = null)"
		/>
	</div>
</template>

<script>
	import ProductDetailForm from "@/components/ProductDetailsForm";
	export default {
		props: ["invoice", "invoiceFieldName", "namespace", "net", "checkQuantity"],

		components: { ProductDetailForm },

		data() {
			return {
				productDetail: null,
				productsFields: [
					{ key: "image", label: "Image" },
					{ key: "name", label: "Name" },
					{ key: "net_cost", label: "Net Unit Cost" },
					{ key: "instock", label: "Instock" },
					{ key: "quantity", label: "Quantity" },
					{ key: "discount", label: "Discount" },
					{ key: "tax", label: "Tax" },
					{ key: "total_cost", label: "Subtotal Cost" },
					{ key: "actions", label: "Actions" }
				]
			};
		},

		computed: {
			isPrice() {
				return this.invoiceFieldName == "price";
			},

			fields() {
				let fields = this.productsFields;
				if (this.isPrice) {
					fields[2] = { key: "net_price", label: "Net Unit Price" };
					fields[7] = { key: "total_price", label: "Subtotal Price" };
				}
				return fields;
			},

			checkQty() {
				return this.checkQuantity == "" || this.checkQuantity == true;
			}
		},

		methods: {
			updateProduct(product) {
				this.invoice.products = this.invoice.products.map((ps) => {
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

			incrementQuantity(row) {
				if (/\d+/.test(row.item.quantity)) {
					if (this.checkQty && row.item.quantity >= row.item.instock) {
						row.item.instockVariant = "outline-danger";
						row.item.incrementBtn = "danger";
						return setTimeout(() => {
							row.item.instockVariant = "outline-success";
							row.item.incrementBtn = "primary";
						}, 1000);
					}
					row.item.quantity += 1;
					row.value += 1;
				} else {
					row.item.quantity = 1;
					row.value = 1;
				}
			},

			decrementQuantity(row) {
				if (/\d+/.test(row.item.quantity) && row.item.quantity - 1 > 0) {
					row.item.quantity -= 1;
					row.value -= 1;
				} else {
					if (row.item.quantity == 1) {
						row.item.decrementBtn = "danger";
						return setTimeout(() => (row.item.decrementBtn = "primary"), 1000);
					}
					row.item.quantity = 1;
					row.value = 1;
				}
			},

			quantityPress(evt) {
				var theEvent = evt || window.event,
					key;

				// Handle paste
				if (theEvent.type === "paste") {
					key = window.event.clipboardData.getData("text/plain");
				} else {
					// Handle key press
					key = theEvent.keyCode || theEvent.which;
					key = String.fromCharCode(key);
				}
				var regex = /[0-9]|\./;
				if (!regex.test(key)) {
					theEvent.returnValue = false;
					if (theEvent.preventDefault) theEvent.preventDefault();
				}
			},

			quantityChanged(row) {
				if (!row.item.quantity || (this.checkQty && row.item.instock < row.item.quantity)) {
					row.item.quantity = row.item.instock < 1 ? row.item.instock : 1;
				}
			},

			removeProduct(row) {
				this.invoice.products = this.invoice.products.filter((_p, i) => i != row.index);
			}
		}
	};
</script>
