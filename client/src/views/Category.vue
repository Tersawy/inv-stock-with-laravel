<template>
	<div>
		<div class="category">Welcome to category page</div>
		<b-btn @click="modal = true" variant="primary">create</b-btn>
		<table class="table mt-5">
			<thead>
				<th v-for="header in headers" :key="header">{{ header }}</th>
			</thead>
			<tbody>
				<tr v-for="category in categories" :key="category.id">
					<td>{{ category.name }}</td>
					<td>{{ category.code }}</td>
					<td>{{ category.created_at }}</td>
					<td>{{ category.deleted }}</td>
					<td>
						<b-btn @click="edit(category)" variant="success">edit</b-btn>
						<b-btn variant="danger" @click="moveToTrash(category)">Trash</b-btn>
					</td>
				</tr>
			</tbody>
		</table>
		<b-modal v-model="modal" @hidden="closeModal">
			<CategoryForm :oldItem="categoryUpdate" @close="closeModal" />
		</b-modal>
	</div>
</template>

<script>
	import CategoryForm from "@/components/CategoryForm.vue";
	import { mapActions, mapState } from "vuex";
	export default {
		name: "Category",

		components: { CategoryForm },

		data: () => ({
			headers: ["name", "code", "created_at", "deleted", "actions"],
			modal: false,
			categoryUpdate: {}
		}),

		mounted() {
			this.getCategories();
		},

		computed: {
			...mapState({
				categories: (state) => state.Category.all.docs
			})
		},

		methods: {
			...mapActions({
				getCategories: "Category/all",
				moveToTrash: "Category/moveToTrash"
			}),

			edit(category) {
				this.categoryUpdate = category;
				this.modal = true;
			},

			closeModal() {
				this.modal = false;
				this.categoryUpdate = {};
			}
		}
	};
</script>
