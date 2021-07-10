<template>
	<v-row justify="center" class="product-form">
		<v-dialog v-model="dialog" max-width="600px">
			<v-card v-if="productDetails">
				<v-card-title>
					<span class="text-h5">{{ productDetails.name }}</span>
				</v-card-title>
				<v-card-text>
					<v-form @submit.prevent="handleSave">
						<v-text-field label="Product Cost" hide-details="auto" v-model.number="productDetails.unit_cost"> </v-text-field>

						<v-text-field label="Order Tax" hide-details="auto" v-model.number="productDetails.tax">
							<v-btn slot="append" color="dark" v-if="productDetails.tax_method === TAX_EXCLUSIVE" @click="changeTaxMethod">
								Exclusive
							</v-btn>
							<v-btn slot="append" color="dark" v-else @click="changeTaxMethod"> Inclusive </v-btn>
						</v-text-field>

						<v-text-field label="Discount" hide-details="auto" v-model.number="productDetails.discount">
							<v-btn slot="append" v-if="productDetails.discount_method === DISCOUNT_FIXED" @click="changeDiscountMethod">
								<v-icon color="dark"> mdi-currency-usd </v-icon>
							</v-btn>
							<v-btn slot="append" color="dark" v-else @click="changeDiscountMethod">
								<v-icon color="dark"> mdi-percent </v-icon>
							</v-btn>
						</v-text-field>
					</v-form>
					<v-card-actions class="mt-5">
						<v-spacer></v-spacer>

						<v-btn color="green darken-1 text-white" small @click="dialog = false"> Close </v-btn>

						<v-btn color="blue darken-1 text-white" small @click="handleSave"> Save </v-btn>
					</v-card-actions>
				</v-card-text>
			</v-card>
		</v-dialog>
	</v-row>
</template>

<script>
	import InputError from "@/components/ui/InputError.vue";
	import DefaultInput from "@/components/ui/DefaultInput.vue";
	import { DISCOUNT_FIXED, TAX_EXCLUSIVE, DISCOUNT_PERCENT, TAX_INCLUSIVE } from "@/helpers/constants";
	export default {
		components: { DefaultInput, InputError },

		props: ["product", "status"],

		data: () => ({
			productDetails: null
		}),

		watch: {
			status(v) {
				this.productDetails = v ? { ...this.product } : null;
			}
		},

		computed: {
			dialog: {
				get() {
					return this.status;
				},
				set() {
					// closed instead of changed coz we don't use Activator change this status to true in this dialog
					this.$emit("closed", false);
				}
			},

			DISCOUNT_FIXED() {
				return DISCOUNT_FIXED;
			},

			TAX_EXCLUSIVE() {
				return TAX_EXCLUSIVE;
			}
		},

		methods: {
			changeDiscountMethod() {
				let isFixed = this.productDetails.discount_method == DISCOUNT_FIXED;

				this.productDetails.discount_method = isFixed ? DISCOUNT_PERCENT : DISCOUNT_FIXED;
			},

			changeTaxMethod() {
				let isInclusive = this.productDetails.tax_method == TAX_INCLUSIVE;

				this.productDetails.tax_method = isInclusive ? TAX_EXCLUSIVE : TAX_INCLUSIVE;
			},

			handleSave() {
				this.$emit("done", this.productDetails);
			}
		}
	};
</script>
