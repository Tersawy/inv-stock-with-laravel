export default {
	data: () => ({
		perPageOptions: [
			{ value: 10, text: 10 },
			{ value: 20, text: 20 },
			{ value: 30, text: 30 },
			{ value: 40, text: 40 },
			{ value: 50, text: 50 }
		],
		tableIsBusy: false,
		perPage: 10,
		page: 1,
		sortBy: "",
		sortDesc: false,
		search: ""
	}),

	mounted() {
		this.getItems(this.queries);
	},

	computed: {
		items() {
			return this.$store.state[this.namespace].all.docs;
		},

		docsCount() {
			return this.$store.state[this.namespace].all.total;
		},

		dirSort() {
			return this.sortDesc ? "desc" : "asc";
		},

		fieldSort() {
			return this.sortBy == "image" ? "id" : this.sortBy;
		},

		queries() {
			let queries = `?page=${this.page}&per_page=${this.perPage}&search=${this.search}&sort_by=${this.fieldSort}&sort_dir=${this.dirSort}`;

			for (let field in this.filterationFields) {
				queries += `&${field}=${this.filterationFields[field]}`;
			}

			return queries;
		}
	},

	watch: {
		search() {
			this.page = 1;
			this.sortBy = "id";
			this.sortDesc = false;
		},
		page() {
			this.finallData();
		},
		perPage() {
			this.page = 1;
		}
	},

	methods: {
		getItems(queries) {
			return this.$store.dispatch(`${this.namespace}/all`, queries);
		},

		moveToTrash(item) {
			return this.$store.dispatch(`${this.namespace}/moveToTrash`, item);
		},

		remove(item) {
			return this.$store.dispatch(`${this.namespace}/remove`, item);
		},

		setOne(data) {
			this.$store.commit(`${this.namespace}/one`, { data });
		},

		contextChanged(ctx) {
			this.sortBy = ctx.sortBy;
			this.sortDesc = ctx.sortDesc;
			this.finallData();
		},

		finallData() {
			this.tableIsBusy = true;

			this.getItems(this.queries);

			this.$nextTick(() => {
				this.tableIsBusy = false;
			});
		}
	}
};
