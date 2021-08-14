<template>
	<div class="warehouse-form">
		<b-container fluid>
			<b-form @submit.prevent="handleSave">
				<DefaultInput type="text" placeholder="Name" v-model="warehouse.name" field="name" />
				<DefaultInput type="tel" placeholder="Phone" v-model="warehouse.phone" field="phone" />
				<DefaultInput type="text" placeholder="Country" v-model="warehouse.country" field="country" />
				<DefaultInput type="text" placeholder="City" v-model="warehouse.city" field="city" />
				<DefaultInput type="email" placeholder="Email" v-model="warehouse.email" field="email" />
				<DefaultInput type="text" placeholder="Zip code" v-model="warehouse.zip_code" field="zip_code" />
				<b-btn :variant="`${warehouseId ? 'success' : 'primary'}`" type="submit">save</b-btn>
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
			warehouse: {
				name: null,
				phone: null,
				country: null,
				city: null,
				email: null,
				zip_code: null
			}
		}),

		async mounted() {
			if (this.oldWarehouse) {
				this.warehouse = this.oldWarehouse;
			} else if (this.warehouseId) {
				await this.getWarehouse(this.warehouseId);
				this.warehouse = this.one;
			}
		},

		computed: {
			...mapState({
				one: (state) => state.Warehouse.one
			}),

			warehouseId() {
				return this.$route.params.warehouseId;
			},

			oldWarehouse() {
				return this.$route.params.warehouse;
			}
		},

		methods: {
			...mapActions({
				create: "Warehouse/create",
				update: "Warehouse/update",
				getWarehouse: "Warehouse/one"
			}),

			handleSave() {
				if (this.warehouseId) return this.handleUpdate();

				return this.handleCreate();
			},

			handleCreate() {
				this.create(this.warehouse);
			},

			handleUpdate() {
				this.update(this.warehouse);
			}
		}
	};
</script>
