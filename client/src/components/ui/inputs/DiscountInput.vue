<template>
	<b-form-group label="Discount" label-for="discount">
		<b-input-group>
			<b-form-input type="number" min="0" id="discount" placeholder="discount" v-model.number="object.discount" />
			<b-input-group-append>
				<b-button variant="outline-primary" @click="changeDiscountMethod">
					<span class="h6 mb-0" v-if="object.discount_method === DISCOUNT_FIXED">$</span>
					<span class="h6 mb-0" v-else>%</span>
				</b-button>
			</b-input-group-append>
		</b-input-group>
	</b-form-group>
</template>

<script>
	import { DISCOUNT_FIXED, DISCOUNT_PERCENT } from "@/helpers/constants";
	export default {
		props: ["object"],
		methods: {
			changeDiscountMethod() {
				this.object.discount_method = this.object.discount_method == DISCOUNT_FIXED ? DISCOUNT_PERCENT : DISCOUNT_FIXED;
			}
		},
		computed: {
			DISCOUNT_FIXED() {
				return DISCOUNT_FIXED;
			},

			DISCOUNT_PERCENT() {
				return DISCOUNT_PERCENT;
			}
		}
	};
</script>
