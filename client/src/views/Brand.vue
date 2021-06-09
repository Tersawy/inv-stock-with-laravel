<template>
	<div>
		<div class="brand">Welcome to brand page</div>
		<b-btn @click="modal = true" variant="primary">create</b-btn>
		<table class="table mt-5">
			<thead>
				<th v-for="header in headers" :key="header">{{ header }}</th>
			</thead>
			<tbody>
				<tr v-for="brand in brands" :key="brand.id">
					<td>
						<div style="max-width: 200px">
							<img :src="APP_BRANDS_URL + brand.image" alt="" class="img-fluid" />
						</div>
					</td>
					<td>{{ brand.name }}</td>
					<td>{{ brand.description }}</td>
					<td>{{ brand.created_at }}</td>
					<td>{{ brand.deleted }}</td>
					<td>
						<b-btn @click="edit(brand)" variant="success">edit</b-btn>
						<b-btn variant="danger" @click="moveToTrash(brand)">Trash</b-btn>
					</td>
				</tr>
			</tbody>
		</table>
		<b-modal v-model="modal" @hidden="closeModal">
			<BrandForm :oldItem="brandUpdate" @close="closeModal" />
		</b-modal>
	</div>
</template>

<script>
	import BrandForm from "@/components/BrandForm.vue";
	import { mapActions, mapState } from "vuex";
	export default {
		name: "Brand",

		components: { BrandForm },

		data: () => ({
			headers: ["image", "name", "description", "created_at", "deleted", "actions"],
			modal: false,
			brandUpdate: {}
		}),

		mounted() {
			this.getBrands();
		},

		computed: {
			...mapState({
				brands: (state) => state.Brand.all.docs
			}),

			APP_BRANDS_URL() {
				return process.env.VUE_APP_BASE_URL + "images/brands/";
			}
		},

		methods: {
			...mapActions({
				getBrands: "Brand/all",
				moveToTrash: "Brand/moveToTrash"
			}),

			edit(brand) {
				this.brandUpdate = brand;
				this.modal = true;
			},

			closeModal() {
				this.modal = false;
				this.brandUpdate = {};
			}
		}
	};
</script>
