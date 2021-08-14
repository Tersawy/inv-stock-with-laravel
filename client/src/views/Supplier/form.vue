<template>
	<div class="supplier-form">
		<b-container fluid>
			<b-form @submit.prevent="handleSave">
				<DefaultInput type="text" placeholder="name" v-model="supplier.name" field="name" />
				<DefaultInput type="email" placeholder="email" v-model="supplier.email" field="email" />
				<DefaultInput type="tel" placeholder="phone" v-model="supplier.phone" field="phone" />
				<DefaultInput type="text" placeholder="country" v-model="supplier.country" field="country" />
				<DefaultInput type="text" placeholder="city" v-model="supplier.city" field="city" />
				<DefaultInput type="text" placeholder="address" v-model="supplier.address" field="address" />
				<b-btn :variant="`${supplierId ? 'success' : 'primary'}`" type="submit">save</b-btn>
			</b-form>
		</b-container>
	</div>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	import DefaultInput from "@/components/ui/DefaultInput.vue";
	import { formMixin } from "@/mixins";
	export default {
		components: { DefaultInput },

		mixins: [formMixin],

		data: () => ({
			supplier: {
				name: null,
				phone: null,
				country: null,
				city: null,
				email: null,
				address: null
			}
		}),

		async mounted() {
			if (this.oldSupplier) {
				this.supplier = this.oldSupplier;
			} else if (this.supplierId) {
				await this.getSupplier(this.supplierId);
				this.supplier = this.one;
			}
		},

		computed: {
			...mapState({
				one: (state) => state.Supplier.one
			}),

			supplierId() {
				return this.$route.params.supplierId;
			},

			oldSupplier() {
				return this.$route.params.supplier;
			}
		},

		methods: {
			...mapActions({
				create: "Supplier/create",
				update: "Supplier/update",
				getSupplier: "Supplier/one"
			}),

			handleSave() {
				if (this.supplierId) return this.handleUpdate();

				return this.handleCreate();
			},

			handleCreate() {
				this.create(this.supplier);
			},

			handleUpdate() {
				this.update(this.supplier);
			}
		}
	};
</script>
