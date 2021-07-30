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

	computed: {
		dirSort() {
			return this.sortDesc ? "desc" : "asc";
		},
		fieldSort() {
			return this.sortBy == "image" ? "id" : this.sortBy;
		},
		queries() {
			let queries = `?page=${this.page}&perPage=${this.perPage}&search=${this.search}&sort_by=${this.fieldSort}&sort_dir=${this.dirSort}`;

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
		}
	},

	methods: {
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
