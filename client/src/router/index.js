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
		path: "/warehouse",
		name: "Warehouse",
		component: require("@/views/Warehouse/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouse/create",
		name: "WarehouseCreate",
		component: require("@/views/Warehouse/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouse/:warehouseId(\\d+)/update",
		name: "WarehouseUpdate",
		component: require("@/views/Warehouse/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouse/trashed",
		name: "WarehouseTrashed",
		component: require("@/views/Warehouse/trash").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/category",
		name: "Category",
		component: require("@/views/Category/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/category/trashed",
		name: "CategoryTrashed",
		component: require("@/views/Category/trash").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/brand",
		name: "Brand",
		component: require("@/views/Brand/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/brand/trashed",
		name: "BrandTrashed",
		component: require("@/views/Brand/trash").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/unit",
		name: "Unit",
		component: require("@/views/Unit/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/product",
		name: "Product",
		component: require("@/views/Product/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/product/create",
		name: "ProductCreate",
		component: require("@/views/Product/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/product/:productId(\\d+)/update",
		name: "ProductUpdate",
		component: require("@/views/Product/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/supplier",
		name: "Supplier",
		component: require("@/views/Supplier/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/supplier/create",
		name: "SupplierCreate",
		component: require("@/views/Supplier/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/supplier/:supplierId(\\d+)/update",
		name: "SupplierUpdate",
		component: require("@/views/Supplier/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/supplier/trashed",
		name: "SupplierTrashed",
		component: require("@/views/Supplier/trash").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/customer",
		name: "Customer",
		component: require("@/views/Customer/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchase",
		name: "Purchase",
		component: require("@/views/Purchase/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchase/create",
		name: "PurchaseCreate",
		component: require("@/views/Purchase/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchase/:invoiceId(\\d+)/update",
		name: "PurchaseUpdate",
		component: require("@/views/Purchase/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchase-return",
		name: "PurchaseReturn",
		component: require("@/views/PurchaseReturn/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchase-return/create",
		name: "PurchaseReturnCreate",
		component: require("@/views/PurchaseReturn/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchase-return/:invoiceId(\\d+)/update",
		name: "PurchaseReturnUpdate",
		component: require("@/views/PurchaseReturn/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sale",
		name: "Sale",
		component: require("@/views/Sale/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sale/create",
		name: "SaleCreate",
		component: require("@/views/Sale/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sale/:invoiceId(\\d+)/update",
		name: "SaleUpdate",
		component: require("@/views/Sale/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sale-return",
		name: "SaleReturn",
		component: require("@/views/SaleReturn/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sale-return/create",
		name: "SaleReturnCreate",
		component: require("@/views/SaleReturn/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/sale-return/:invoiceId(\\d+)/update",
		name: "SaleReturnUpdate",
		component: require("@/views/SaleReturn/form").default,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/currency",
		name: "Currency",
		component: require("@/views/Currency/index").default,
		meta: { auth: true, userType: [0, 1, 2] }
	}
];

const router = new VueRouter({
	mode: "history",
	base: process.env.BASE_URL,
	routes
});

export default router;
