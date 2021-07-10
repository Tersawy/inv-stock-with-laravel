<template>
	<div>
		<div class="product">Welcome to product page</div>
		<v-btn :to="{ name: 'ProductCreate' }" elevation="1" color="primary" link>Create a new product</v-btn>
		<table class="table mt-5">
			<thead>
				<th v-for="header in headers" :key="header" class="text-center">{{ header }}</th>
			</thead>
			<tbody>
				<tr v-for="product in products" :key="product.id">
					<!-- <td><Barcode :value="product.code" :format="product.barcode_type" /></td> -->
					<td>
						<div class="text-center" v-if="product.image">
							<img :src="APP_PRODUCTS_URL + product.image" width="70" height="50" />
						</div>
					</td>
					<td class="text-center">{{ product.name }}</td>
					<td class="text-center">{{ product.code }}</td>
					<td class="text-center">{{ product.price }}</td>
					<td class="text-center">{{ product.quantity }}</td>
					<td class="text-center">{{ product.category }}</td>
					<td class="text-center">{{ product.brand }}</td>
					<td class="text-center">{{ product.unit }}</td>
					<td class="text-center">
						<router-link :to="{ name: 'ProductUpdate', params: { productId: product.id, product } }" custom v-slot="{ navigate }">
							<b-btn @click="navigate" @keypress.enter="navigate" role="link" variant="success">edit</b-btn>
						</router-link>
						<b-btn variant="danger" @click="moveToTrash(product)">Trash</b-btn>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	import Barcode from "vue-barcode";
	import { mapActions, mapState } from "vuex";
	export default {
		name: "Product",

		components: { Barcode },

		data: () => ({
			headers: ["image", "name", "code", "price", "quantity", "category", "brand", "unit", "actions"]
		}),

		mounted() {
			this.getProducts();
		},

		computed: {
			...mapState({
				products: (state) => state.Product.all.docs
			}),

			APP_PRODUCTS_URL() {
				return process.env.VUE_APP_BASE_URL + "images/products/";
			}
		},

		methods: {
			...mapActions({
				getProducts: "Product/all",
				moveToTrash: "Product/moveToTrash"
			})
		}
	};
	/* 
		Code 128 	=> 8
		Code 39 	=> 8
		EAN8 			=> 7
		EAN13 		=> 12
		UPC 			=> 11
	*/
</script>
