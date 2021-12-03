import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

const routes = [
	{
		path: "/login",
		name: "Login",
		component: require("@/views/Auth/login").default,
		meta: { auth: false }
	},
	{
		path: "/",
		name: "Dashboard",
		component: require("@/views/Dashboard").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouses",
		name: "Warehouses",
		component: require("@/views/Warehouses/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouses/create",
		name: "WarehousesCreate",
		component: require("@/views/Warehouses/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouses/:warehouseId(\\d+)/edit",
		name: "WarehousesEdit",
		component: require("@/views/Warehouses/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouses/trashed",
		name: "WarehousesTrashed",
		component: require("@/views/Warehouses/trash").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/categories",
		name: "Categories",
		component: require("@/views/Categories/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/categories/trashed",
		name: "CategoriesTrashed",
		component: require("@/views/Categories/trash").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/brands",
		name: "Brands",
		component: require("@/views/Brands/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/brands/trashed",
		name: "BrandsTrashed",
		component: require("@/views/Brands/trash").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/units",
		name: "Units",
		component: require("@/views/Units/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/products",
		name: "Products",
		component: require("@/views/Products/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/products/create",
		name: "ProductsCreate",
		component: require("@/views/Products/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/products/:productId(\\d+)/edit",
		name: "ProductsEdit",
		component: require("@/views/Products/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/suppliers",
		name: "Suppliers",
		component: require("@/views/Suppliers/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/suppliers/create",
		name: "SuppliersCreate",
		component: require("@/views/Suppliers/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/suppliers/:supplierId(\\d+)/edit",
		name: "SuppliersEdit",
		component: require("@/views/Suppliers/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/suppliers/trashed",
		name: "SuppliersTrashed",
		component: require("@/views/Suppliers/trash").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/customers",
		name: "Customers",
		component: require("@/views/Customers/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchases",
		name: "Purchases",
		component: require("@/views/Purchases/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchases/create",
		name: "PurchasesCreate",
		component: require("@/views/Purchases/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchases/:invoiceId(\\d+)/edit",
		name: "PurchasesEdit",
		component: require("@/views/Purchases/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchases-return",
		name: "PurchasesReturn",
		component: require("@/views/PurchasesReturn/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchases-return/create",
		name: "PurchasesReturnCreate",
		component: require("@/views/PurchasesReturn/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchases-return/:invoiceId(\\d+)/edit",
		name: "PurchasesReturnEdit",
		component: require("@/views/PurchasesReturn/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sales",
		name: "Sales",
		component: require("@/views/Sales/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sales/create",
		name: "SalesCreate",
		component: require("@/views/Sales/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sales/:invoiceId(\\d+)/edit",
		name: "SalesEdit",
		component: require("@/views/Sales/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sales-return",
		name: "SalesReturn",
		component: require("@/views/SalesReturn/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sales-return/create",
		name: "SalesReturnCreate",
		component: require("@/views/SalesReturn/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sales-return/:invoiceId(\\d+)/edit",
		name: "SalesReturnEdit",
		component: require("@/views/SalesReturn/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/currencies",
		name: "Currencies",
		component: require("@/views/Currencies/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	}
];

const router = new VueRouter({
	mode: "history",
	base: process.env.BASE_URL,
	routes
});

export default router;
